<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;

class SchemaBuilder
{
    private ?Schema $currentSchema = null;
    private ?PropertyInterface $currentProperty = null;

    public function __construct(private $noMedia = true)
    {
        //
    }

    public function addSchema(Schema $schema, string $className): bool
    {
        if (!$schema->getName()) {
            $explodedNamespace = explode('\\', $className);
            $className = end($explodedNamespace);
            $schema->setName($className);
        }

        $this->currentSchema = $schema;

        return true;
    }

    /**
     * @throws IllegalFieldException
     */
    public function addProperty(PropertyInterface $property): bool
    {
        if (!$this->currentSchema) {
            throw IllegalFieldException::missingSchema();
        }

        $this->currentSchema->addProperty($property);
        $this->currentProperty = $property;

        return true;
    }

    /**
     * @throws IllegalFieldException
     */
    public function addPropertyItems(PropertyItems $items): bool
    {
        if (!$this->currentProperty instanceof Property) {
            throw IllegalFieldException::missingArrayProperty();
        }

        $this->currentProperty->setPropertyItems($items);

        return true;
    }

    public function getComponent(): ?Schema
    {
        if ($this->noMedia) {
            $this->currentSchema?->setNoMedia(true);
        }

        return $this->currentSchema;
    }
}
