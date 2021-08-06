<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\HttpFoundation\Request;

class GeneratorHttp
{
    private array $paths = [];

    public function append(ReflectionClass $reflectionClass)
    {
        foreach ($reflectionClass->getMethods() as $method) {
            $methodAttributes = $method->getAttributes();

            $route = array_filter($methodAttributes, fn(ReflectionAttribute $attribute) => $attribute->getName() === Route::class);

            if (count($route) < 1) {
                continue;
            }

            $parameters = $this->getParameters($method->getParameters());

            $pathBuilder = new PathMethodBuilder();
            $pathBuilder->setRequestBody(new RequestBody());

            // Add method Attributes to the builder
            foreach ($methodAttributes as $attribute) {
                $name = $attribute->getName();
                $instance = $attribute->newInstance();

                match ($name) {
                    Route::class => $pathBuilder->setRoute($instance, $parameters),
                    RequestBody::class => $pathBuilder->setRequestBody($instance),
                    Property::class => $pathBuilder->addProperty($instance),
                    PropertyItems::class => $pathBuilder->setPropertyItems($instance),
                    MediaProperty::class => $pathBuilder->setMediaProperty($instance),
                    Response::class => $pathBuilder->setResponse($instance),
                };
            }

            $route = $pathBuilder->getRoute();
            if ($route) {
                $this->paths[] = $route;
            }
        }
    }

    private function getParameters(array $methodParameters): array
    {
        return array_map(
            function (ReflectionParameter $param) {
                return array_map(
                    function (ReflectionAttribute $attribute) use ($param) {
                        $instance = $attribute->newInstance();
                        $instance->setName($param->getName());
                        $instance->setParamType((string) $param->getType());

                        return $instance;
                    },
                    $param->getAttributes(Parameter::class, ReflectionAttribute::IS_INSTANCEOF)
                );
            },
            $methodParameters
        );
    }

    public function build(): array
    {
        $paths = [];
        foreach ($this->paths as $path) {
            if (!isset($paths[$path->getRoute()])) {
                $paths = array_merge($paths, $path->jsonSerialize());
            } else {
                $paths = $this->mergeRoutes($paths, $path);
            }
        }

        return ["paths" => $paths];
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
