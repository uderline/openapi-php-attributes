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

/**
 * This represents an OpenAPI method route (GET, POST, PUT, PATCH or DELETE path).
 * Methods are merged in the Path
 */
class Method implements JsonSerializable
{
    private Route $route;
    /** @var Parameter[] */
    private array $parameters = [];
    private ?Response $response = null;
    private ?RequestBody $requestBody = null;
    private ?DynamicMethodResolverInterface $dynamicBuilder = null;
    private ?PropertyInterface $lastProperty = null;

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

    public function setRequestBody(RequestBody $requestBody): self
    {
        $this->requestBody = $requestBody;
        $this->lastProperty = null;

        return $this;
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

    /**
     * @throws DefinitionCheckerException
     */
    public function addProperty(PropertyInterface $property): self
    {
        if ($this->response) {
            $this->response->addProperty($property);
        } elseif ($this->requestBody) {
            $this->requestBody->addProperty($property);
        } else {
            throw DefinitionCheckerException::missingField("response or body");
        }

        $this->lastProperty = $property;

        return $this;
    }

    /**
     * @throws IllegalFieldException
     */
    public function addPropertyItemsToLastProperty(PropertyItems $propertyItems): self
    {
        if ($this->lastProperty && $this->lastProperty?->getType() !== Type::ARRAY) {
            throw IllegalFieldException::missingArrayProperty();
        }

        if ($this->response) {
            $this->response->addProperty($propertyItems);
        } elseif ($this->requestBody) {
            $this->requestBody->addProperty($propertyItems);
        } else {
            throw DefinitionCheckerException::missingField("response or body");
        }

        if ($this->lastProperty) {
            if (!$this->lastProperty instanceof Property) {
                throw IllegalFieldException::missingArrayProperty();
            }

            $this->lastProperty->setPropertyItems($propertyItems);
        } else {
            $this->lastProperty = $propertyItems;
        }

        return $this;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;
        $this->lastProperty = null;

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
