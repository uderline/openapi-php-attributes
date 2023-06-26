<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use JsonSerializable;
use OpenApiGenerator\Attributes\DynamicBuilder;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use ReflectionClass;
use ReflectionMethod;

/**
 * This represents an OpenAPI method route (GET, POST, PUT, PATCH or DELETE path).
 * Methods are merged in the Path
 */
class Method implements JsonSerializable
{
    private Route $route;
    /** @var Parameter[] */
    private array $parameters = [];
    /** @var Response[] */
    private array $responses = [];
    private ?RequestBody $requestBody = null;
    private ?DynamicMethodResolverInterface $dynamicBuilder = null;
    private ?PropertyInterface $lastProperty = null;

    public function getPath(): string
    {
        return $this->route->getRoute();
    }

    public function getMethod(): string
    {
        return $this->route->getMethod();
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function setRoute(Route $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function getRequestBody(): ?RequestBody
    {
        return $this->requestBody;
    }

    public function setRequestBody(RequestBody $requestBody): self
    {
        $this->requestBody = $requestBody;
        $this->lastProperty = null;

        return $this;
    }

    public function addParameter(Parameter $parameter): self
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * @throws IllegalFieldException
     * @throws DefinitionCheckerException
     */
    public function addPropertyItems(PropertyItems $propertyItems): self
    {
        if ($this->lastProperty && $this->lastProperty?->getType() !== Type::ARRAY) {
            throw IllegalFieldException::missingArrayProperty();
        }

        if ($this->lastProperty) {
            if (!$this->lastProperty instanceof Property) {
                throw IllegalFieldException::missingArrayProperty();
            }

            $this->lastProperty->setPropertyItems($propertyItems);
        } else {
            if ($this->responses) {
                $response = end($this->responses);
                $response->addProperty($propertyItems);
            } elseif ($this->requestBody) {
                $this->requestBody->addProperty($propertyItems);
            } else {
                throw DefinitionCheckerException::missingField("response or body");
            }

            $this->lastProperty = $propertyItems;
        }

        return $this;
    }

    /**
     * @throws DefinitionCheckerException
     */
    public function addProperty(PropertyInterface $property): self
    {
        if ($this->responses) {
            $response = end($this->responses);
            $response->addProperty($property);
        } elseif ($this->requestBody) {
            $this->requestBody->addProperty($property);
        } else {
            throw DefinitionCheckerException::missingField("response or body");
        }

        $this->lastProperty = $property;

        return $this;
    }

    public function addResponse(Response $response): self
    {
        $this->responses[] = $response;
        $this->lastProperty = null;

        return $this;
    }

    public function setDynamicBuilder(
        DynamicBuilder $instance,
        ReflectionClass $reflectionClass,
        ReflectionMethod $reflectionMethod
    ): void {
        $this->dynamicBuilder = new $instance->builder();
        $this->dynamicBuilder->setReflectionClass($reflectionClass);
        $this->dynamicBuilder->setReflectionMethod($reflectionMethod);
        $this->dynamicBuilder->setMethod($this);
    }

    public function jsonSerialize(): array
    {
        $this->dynamicBuilder ??= (new DefaultDynamicMethodResolver())->setMethod($this);

        return $this->dynamicBuilder->build();
    }
}
