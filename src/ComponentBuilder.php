<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;

class ComponentBuilder
{
    private ?Schema $currentSchema = null;
    private ?PropertyInterface $currentProperty = null;

    public function __construct(private $noMedia = true)
    {
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

    public function addProperty(PropertyInterface $property): bool
    {
        $this->saveProperty();
        $this->currentProperty = $property;

        return true;
    }

    private function saveProperty(): void
    {
        if (!$this->currentProperty) {
            return;
        }

        $this->currentSchema->addProperty($this->currentProperty);
        $this->currentProperty = null;
    }

    public function addPropertyItems(PropertyItems $items): bool
    {
        $this->currentProperty->setPropertyItems($items);
        $this->saveProperty();

        return true;
    }

    public function getComponent(): ?Schema
    {
        $this->saveProperty();
        $this->currentSchema->setNoMedia($this->noMedia);

        return $this->currentSchema;
    }
}
