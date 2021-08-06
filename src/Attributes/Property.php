<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use OpenApiGenerator\Types\PropertyType;
use JsonSerializable;

/**
 * This represents an open api property.
 * The property must have a type and a property name and can have a description and an example
 * If the property is an array, a PropertyItems must be set
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class Property implements PropertyInterface, JsonSerializable
{
    private ?PropertyItems $propertyItems = null;

    public function __construct(
        private string $type,
        private string $property,
        private string $description = '',
        private mixed $example = null,
        private ?string $format = null,
        private ?array $enum = null
    ) {
    }

    public function setPropertyItems(PropertyItems $propertyItems): void
    {
        $this->propertyItems = $propertyItems;
        $this->propertyItems->setExample($this->example);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function jsonSerialize(): array
    {
        if ($this->type === PropertyType::ARRAY) {
            if ($this->propertyItems) {
                return $this->propertyItems->jsonSerialize();
            }
        }

        $array = [
            'type' => $this->type,
            'description' => $this->description
        ];

        if ($this->format) {
            $array['format'] = $this->format;
        }

        if ($this->enum) {
            $array['enum'] = $this->enum;
        }

        return $array;
    }
}
