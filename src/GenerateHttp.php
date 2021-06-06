<?php

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use Illuminate\Http\Request;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;

class GenerateHttp
{
    private array $paths = [];

    public function append(ReflectionClass $reflectionClass)
    {
        foreach ($reflectionClass->getMethods() as $method) {
            $methodAttributes = $method->getAttributes();
            $methodParameters = $method->getParameters();

            $route = array_filter($methodAttributes, fn(ReflectionAttribute $attribute) => $attribute->getName() === Route::class);

            if (count($route) < 1) {
                continue;
            }

            $parameters = array_map(
                function (ReflectionParameter $param) {
                    return array_map(
                        function (ReflectionAttribute $attribute) use ($param) {
                            $instance = $attribute->newInstance();
                            $instance->setName($param->getName());
                            $instance->setParamType($param->getType());

                            return $instance;
                        },
                        $param->getAttributes(Parameter::class, ReflectionAttribute::IS_INSTANCEOF)
                    );
                },
                $methodParameters
            );

            $pathBuilder = new PathMethodBuilder();

            // Look for a Request type parameter and add it to the builder
            foreach ($methodParameters as $param) {
                if ((string)$param->getType() !== Request::class) {
                    continue;
                }

                // Check if the parameter has an attribute
                $paramAttributes = $param->getAttributes(RequestBody::class, ReflectionAttribute::IS_INSTANCEOF);
                $requestBodyAttr = array_filter(
                    $paramAttributes,
                    function (ReflectionAttribute $attribute) {
                        return ($attribute->getName() === RequestBody::class) ? $attribute : null;
                    }
                );

                // If so, use it. Otherwise, set a default one
                if (count($requestBodyAttr) === 1) {
                    $pathBuilder->setRequestBody(reset($requestBodyAttr)->newInstance());
                    break;
                }

                $pathBuilder->setRequestBody(new RequestBody());
                break;
            }

            // Add method Attributes to the builder
            foreach ($methodAttributes as $attribute) {
                $name = $attribute->getName();
                $instance = $attribute->newInstance();

                match ($name) {
                    Route::class => $pathBuilder->setRoute($instance, $parameters),
                    Property::class => $pathBuilder->addProperty($instance),
                    Response::class => $pathBuilder->setResponse($instance),
                    PropertyItems::class => $pathBuilder->setPropertyItems($instance),
                    default => true
                };
            }

            $requestBodyAttr = null;

            $route = $pathBuilder->getRoute();
            if ($route) {
                $this->paths[] = $route;
            }
        }
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
