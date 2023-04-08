<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use JsonSerializable;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;

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
    private Response $response;
    private ?RequestBody $requestBody = null;

    public function getPath(): string
    {
        return $this->route->getPath();
    }

    public function getMethod(): string
    {
        return $this->route->getMethod();
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
        $this->properties[] = $property;

        return $this;
    }

    public function addPropertyItemsToLastProperty(PropertyItems $propertyItems): self
    {
        $this->properties[count($this->properties) - 1]?->setPropertyItems($propertyItems);

        return $this;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * A method contains:
     * - a schema that describes the request body
     * - a request body that contains the schema
     * - a route that contains (but not only) the path, method, tags, summary, parameters and the request body
     */
    public function jsonSerialize(): array
    {
        $schema = new Schema();
        array_walk($this->properties, $schema->addProperty(...));

        $this->requestBody ??= new RequestBody();
        $this->requestBody->setSchema($schema);

        $route = clone $this->route;
        $route->setGetParams($this->parameters);
        $route->setRequestBody($this->requestBody);
        $route->addResponse($this->response);

        return $route->jsonSerialize();
    }
}
