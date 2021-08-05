<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\SchemaType;

/**
 * This represents an OpenAPI path which has a route with only ONE method (GET, POST, PUT or PATCH)
 * Paths are merged in the generator
 */
class PathMethodBuilder
{
    private ?Route $currentRoute = null;
    private ?Schema $currentSchema = null;
    private Response|RequestBody|null $currentSchemaHolder = null;
    private Property|MediaProperty|null $currentProperty = null;

    /**
     * Set the current route with GET parameters
     *
     * @param Route $route
     * @param array $params
     */
    public function setRoute(Route $route, array $params): void
    {
        $route->setGetParams($params);

        $this->currentRoute = $route;
    }

    /**
     * Set the request body which is used to wrap properties
     *
     * @param RequestBody $requestBody
     */
    public function setRequestBody(RequestBody $requestBody): void
    {
        $this->currentSchemaHolder = $requestBody;
    }

    /**
     * Add a property. When adding a property, the previous one is saved and added to the schema
     * A schema is a set of properties and describes, for example, requests and responses
     *
     * @param Property $property
     */
    public function addProperty(Property $property): void
    {
        $this->currentProperty = $property;
        $this->saveProperty();
    }

    /**
     * Save the property into a schema.
     * If the property is an array, don't nullify the current property as it should be followed by
     * the PropertyItems attribute
     */
    private function saveProperty(): void
    {
        if (!$this->currentSchema) {
            if ($this->currentProperty instanceof Property) {
                if ($this->currentProperty->getType() === PropertyType::ARRAY) {
                    $this->addSchema(new Schema(SchemaType::ARRAY));
                } else {
                    $this->addSchema(new Schema(SchemaType::OBJECT));
                }
            } elseif ($this->currentProperty instanceof MediaProperty) {
                $this->addSchema(new Schema(SchemaType::OBJECT));
            }
        }

        $this->currentSchema->addProperty($this->currentProperty);

        if ($this->currentProperty->getType() !== PropertyType::ARRAY) {
            $this->currentProperty = null;
        }
    }

    /**
     * Adding a schema should only be done internally when making paths.
     *
     * @param Schema $schema
     */
    private function addSchema(Schema $schema): void
    {
        $this->currentSchema = $schema;
    }

    /**
     * Add the response part of the path/method
     *
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->saveSchemaHolder();

        $this->currentSchemaHolder = $response;
    }

    /**
     * Save the schema holder by setting the current schema to the schema holder and adding it to the current route
     */
    private function saveSchemaHolder(): void
    {
        if ($this->currentSchemaHolder) {
            if ($this->currentSchema) {
                $this->currentSchemaHolder->setSchema($this->currentSchema);
            }

            if ($this->currentSchemaHolder instanceof RequestBody) {
                $this->currentRoute->setRequestBody($this->currentSchemaHolder);
            } else {
                $this->currentRoute->setResponse($this->currentSchemaHolder);
            }

            $this->currentSchemaHolder = null;
            $this->currentSchema = null;
        }
    }

    /**
     * This should only appear if the current property is an array
     * TODO: throw an exception if the current property is null or not an array type
     *
     * @param PropertyItems $propertyItems
     */
    public function setPropertyItems(PropertyItems $propertyItems)
    {
        $this->currentProperty->setPropertyItems($propertyItems);
        $this->saveProperty();
        $this->currentProperty = null;
    }

    /**
     * Finally, get the route
     * TODO: return Route and not null. If null, throw an exception
     *
     * @return Route|null
     */
    public function getRoute(): ?Route
    {
        $this->saveSchemaHolder();

        return $this->currentRoute;
    }

    public function setMediaProperty(MediaProperty $property): void
    {
        $this->currentProperty = $property;
        $this->saveProperty();
    }
}
