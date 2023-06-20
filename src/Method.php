<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use JsonSerializable;
use OpenApiGenerator\Attributes\DynamicBuilder;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;

/**
 * This represents an OpenAPI method route (GET, POST, PUT, PATCH or DELETE path).
 * Methods are merged in the Path
 */
class Method implements JsonSerializable
{
    private Route $route;
    /** @var Parameter[] */
    private array $parameters = [];
    /** @var PropertyInterface[] */
    private array $properties = [];
    private ?Response $response = null;
    private ?RequestBody $requestBody = null;
    private ?DynamicMethodResolverInterface $dynamicBuilder = null;
    private PropertyInterface $lastProperty;

    public function getPath(): string
    {
        return $this->route->getPath();
    }

    public function getMethod(): string
    {
        return $this->route->getMethod();
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getRequestBody(): ?RequestBody
    {
        return $this->requestBody;
    }

    public function setRequestBody(RequestBody $requestBody): void
    {
        $this->requestBody = $requestBody;
    }

    public function setRoute(Route $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function addParameter(Parameter $parameter): self
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    public function addProperty(PropertyInterface $property): self
    {
        if ($this->response) {
            $this->response->addProperty($property);
        } elseif ($this->requestBody) {
            $this->requestBody->addProperty($property);
        } else {
            echo "No response or requestBody found for property\n";
        }

        $this->lastProperty = $property;

        return $this;
    }

    public function addPropertyItemsToLastProperty(PropertyItems $propertyItems): self
    {
        $this->lastProperty->setPropertyItems($propertyItems);

        return $this;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function setDynamicBuilder(
        DynamicBuilder $instance,
        \ReflectionClass $reflectionClass,
        \ReflectionMethod $reflectionMethod
    ): void {
        $this->dynamicBuilder = new $instance->builder;
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
