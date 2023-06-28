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
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is the class that will generate the OpenAPI paths (e.g. /users/{id})
 * and its methods (e.g. GET, POST, PUT, PATCH, DELETE)
 */
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

    public function append(ReflectionClass $reflectionClass): void
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
            } else {
                $requestBody = array_filter(
                    $methodAttributes,
                    static fn(ReflectionAttribute $attribute) => $attribute->getName() === RequestBody::class
                );
                $requestBody = reset($requestBody);
                $requestBody = $requestBody ? $requestBody->newInstance() : new RequestBody();

                $method->setRequestBody($requestBody);
            }

            $parameters = $this->getParametersFromReflectionParameter($reflectionMethod->getParameters());
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
                    Property::class, MediaProperty::class => $method->addProperty($instance),
                    PropertyItems::class => $method->addPropertyItems($instance),
                    Response::class => $method->addResponse($instance),
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

    /**
     * OPAG supports Symfony Request class. This method will build the RequestBody from the Symfony Request class
     * The request object will be added as a reference to the RequestBody
     */
    private function buildRequestBodyFromSymfonyRequest(ReflectionMethod $method): ?RequestBody
    {
        $requestBody = new RequestBody();

        // Get the first parameter that is a subclass of Symfony Request
        $symfonyRequests = array_filter(
            $method->getParameters(),
            fn(ReflectionParameter $parameter): bool => is_subclass_of($parameter->getType()->getName(), Request::class)
        );
        if (!count($symfonyRequests)) {
            return null;
        }

        $symfonyRequest = reset($symfonyRequests);
        if (empty((new ReflectionClass($symfonyRequest->getType()->getName()))->getAttributes(Schema::class))) {
            return null;
        }

        $schema = new Schema();
        $schema->setNoMedia(true);
        $requestBody->setSchema($schema);
        $property = new RefProperty($symfonyRequest->getType()->getName());
        $property->setComponentRoutePrefix("#/components/requestBodies/");
        $requestBody->addProperty($property);

        return $requestBody;
    }

    /**
     * @param ReflectionParameter[] $methodParameters
     * @return Parameter[]
     */
    private function getParametersFromReflectionParameter(array $methodParameters): array
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

    /**
     * Return the Path object that represents an endpoint containing methods (GET, POST, PUT, DELETE, PATCH)
     */
    private function getPathFromMethod(Method $method): Path|false
    {
        $existingPath = array_filter($this->paths, static fn(Path $path) => $path->hasSamePath($method));

        return reset($existingPath);
    }
}
