<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\PATCH;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\POST;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\RefProperty;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Tests\Examples\Dummy\DummyRequest;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\HttpFoundation\Request;

class GeneratorHttp
{
    private array $paths = [];

    public function append(ReflectionClass $reflectionClass)
    {
        foreach ($reflectionClass->getMethods() as $method) {
            $methodAttributes = $method->getAttributes();

            $routeAttributeNames = [Route::class, GET::class, POST::class, PUT::class, DELETE::class, PATCH::class];
            $route = array_filter(
                $methodAttributes,
                static fn(ReflectionAttribute $attribute) => in_array($attribute->getName(), $routeAttributeNames)
            );

            if (count($route) < 1) {
                continue;
            }

            $parameters = $this->getParameters($method->getParameters());
            $pathParameters = $method->getAttributes(PathParameter::class);

            if ($pathParameters) {
                foreach ($pathParameters as $attribute) {
                    $parameters[] = $attribute->newInstance();
                }
            }

            $requestBody = $this->getRequestBody($method);

            $pathBuilder = new PathMethodBuilder();
            $pathBuilder->setRequestBody($requestBody);

            // Add method Attributes to the builder
            foreach ($methodAttributes as $attribute) {
                $name = $attribute->getName();
                /** @var Route|RequestBody|Property|PropertyItems|MediaProperty|Response $instance */
                $instance = $attribute->newInstance();

                match ($name) {
                    Route::class,
                    GET::class,
                    POST::class,
                    PUT::class,
                    DELETE::class,
                    PATCH::class => $pathBuilder->setRoute($instance, $parameters),
                    RequestBody::class => $pathBuilder->setRequestBody($instance),
                    Property::class => $pathBuilder->addProperty($instance),
                    PropertyItems::class => $pathBuilder->setPropertyItems($instance),
                    MediaProperty::class => $pathBuilder->setMediaProperty($instance),
                    Response::class => $pathBuilder->setResponse($instance),
                    default => null
                };
            }

            $route = $pathBuilder->getRoute();
            if ($route) {
                $this->paths[] = $route;
            }
        }
    }

    private function getRequestBody(ReflectionMethod $method): RequestBody
    {
        $requestBody = new RequestBody();

        $requestClass = array_filter(
            $method->getParameters(),
            static fn(ReflectionParameter $parameter): bool => is_subclass_of(
                $parameter->getType()->getName(),
                Request::class
            )
        );

        if (count($requestClass) > 0) {
            $requestReflection = new ReflectionClass(DummyRequest::class);
            $schemaAttributes = $requestReflection->getAttributes(Schema::class);
            /** @var ReflectionAttribute|false $schema */
            $schema = reset($schemaAttributes);

            if ($schema) {
                /** @var Schema $requestSchema */
                $requestSchema = $schema->newInstance();

                $builder = new ComponentBuilder(false);
                $builder->addSchema($requestSchema, DummyRequest::class);
                $builder->addProperty(new RefProperty($requestSchema->getName()));

                $requestBody->setSchema($builder->getComponent());
            }
        }

        return $requestBody;
    }

    /**
     * @param ReflectionParameter[] $methodParameters
     * @return Parameter[]
     */
    private function getParameters(array $methodParameters): array
    {
        return array_filter(
            array_map(
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
            )
        );
    }

    public function build(): array
    {
        $paths = [];

        foreach ($this->paths as $path) {
            $paths = isset($paths[$path->getRoute()])
                ? $this->mergeRoutes($paths, $path)
                : array_merge($paths, $path->jsonSerialize());
        }

        return $paths;
    }

    private function mergeRoutes($paths, Route $route): array
    {
        $toMerge = $route->jsonSerialize();
        $routeToMerge = reset($toMerge);
        $methodToMerge = reset($routeToMerge);
        $paths[$route->getRoute()][$route->getMethod()] = $methodToMerge;

        return $paths;
    }
}
