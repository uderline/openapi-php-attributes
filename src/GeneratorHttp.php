<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\DynamicBuilder;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\PATCH;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\POST;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\RefProperty;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\HttpFoundation\Request;

class GeneratorHttp
{
    /** @var Path[] $paths */
    private array $paths = [];

    public function build(): array
    {
        $paths = [];
        foreach ($this->paths as $path) {
            $paths[$path->getPath()] = $path->jsonSerialize();
        }

        return $paths;
    }

    public function append(ReflectionClass $reflectionClass)
    {
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodAttributes = $reflectionMethod->getAttributes();

            $routeAttributeNames = [Route::class, GET::class, POST::class, PUT::class, DELETE::class, PATCH::class];
            $route = array_filter(
                $methodAttributes,
                static fn(ReflectionAttribute $attribute) => in_array($attribute->getName(), $routeAttributeNames)
            );

            if (!count($route)) {
                continue;
            }

            $method = new Method();

            if ($symfonyRequest = $this->buildRequestBodyFromSymfonyRequest($reflectionMethod)) {
                $method->setRequestBody($symfonyRequest);
            }

            $parameters = $this->getReflectionParameters($reflectionMethod->getParameters());
            array_walk($parameters, $method->addParameter(...));

            // Add method Attributes to the builder
            foreach ($methodAttributes as $attribute) {
                $name = $attribute->getName();
                /** @var Route|RequestBody|PropertyInterface|Response|PathParameter $instance */
                $instance = $attribute->newInstance();

                match ($name) {
                    Route::class,
                    GET::class,
                    POST::class,
                    PUT::class,
                    DELETE::class,
                    PATCH::class => $method->setRoute($instance),
                    PathParameter::class => $method->addParameter($instance),
                    RequestBody::class => $method->setRequestBody($instance),
                    Property::class, MediaProperty::class => $method->addProperty($instance),
                    PropertyItems::class => $method->addPropertyItemsToLastProperty($instance),
                    Response::class => $method->setResponse($instance),
                    DynamicBuilder::class => $method->setDynamicBuilder($instance, $reflectionClass, $reflectionMethod),
                    default => null
                };
            }

            if ($existingPath = $this->getPathFromMethod($method)) {
                $existingPath->addMethod($method);
                continue;
            }

            $this->paths[] = new Path($method);
        }
    }

    private function getPathFromMethod(Method $method): Path|false
    {
        $existingPath = array_filter($this->paths, static fn(Path $path) => $path->hasSamePath($method));
        return reset($existingPath);
    }

    private function buildRequestBodyFromSymfonyRequest(ReflectionMethod $method): ?RequestBody
    {
        $requestBody = new RequestBody();

        // Get the first parameter that is a subclass of Symfony Request
        $requestClass = array_filter(
            $method->getParameters(),
            static fn(ReflectionParameter $parameter): bool => is_subclass_of(
                $parameter->getType()->getName(),
                Request::class
            )
        );
        $requestClass = reset($requestClass);
        if (!$requestClass) {
            return null;
        }

        // Get the Schema attribute from the Symfony Request class
        $requestReflection = new ReflectionClass($requestClass->getType()->getName());
        $schemaAttributes = $requestReflection->getAttributes(Schema::class);
        $schemaAttribute = reset($schemaAttributes);
        if (!$schemaAttribute) {
            return null;
        }

        // Build the schema
        $schema = $schemaAttribute->newInstance();
        $builder = new SchemaBuilder(false);
        $builder->addSchema($schema, Request::class);
        $builder->addProperty(new RefProperty($schema->getName()));

        // Set the schema to the RequestBody and return it
        $requestBody->setSchema($builder->getComponent());

        return $requestBody;
    }

    /**
     * @param ReflectionParameter[] $methodParameters
     * @return Parameter[]
     */
    private function getReflectionParameters(array $methodParameters): array
    {
        $methodParameters = array_map(
            static function (ReflectionParameter $param) {
                $attributes = $param->getAttributes(Parameter::class, ReflectionAttribute::IS_INSTANCEOF);
                if (!$attributes) {
                    return null;
                }
                $instance = $attributes[0]->newInstance();
                $instance->setName($param->getName());
                $instance->setParamType((string)$param->getType());
                return $instance;
            },
            $methodParameters
        );

        return array_filter($methodParameters);
    }
}
