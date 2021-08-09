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
        private ?array $enum = null,
        private mixed $properties = null,
    ) {
    }

    public function createFromArray(array $data): self
    {
        $args = [];
        $format = [
            'type' => '',
            'property' => '',
            'description' => '',
            'example' => null,
            'format' => null,
            'enum' => null,
            'properties' => null,
        ];

        foreach ($format as $key => $default) {
            $args[] = array_key_exists($key, $data) ? $data[$key] : $default;
        }

        return new self(...$args);
    }

    public function setPropertyItems(PropertyItems $propertyItems): void
    {
        $this->propertyItems = $propertyItems;
        $this->propertyItems->setExample($this->example);
    }

    public function getType(): string
    {
        return $this->properties ? PropertyType::OBJECT : $this->property;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function jsonSerialize(): array
    {
        if ($this->getType() === PropertyType::ARRAY) {
            if ($this->propertyItems) {
                return $this->propertyItems->jsonSerialize();
            }
        }

        $data = [
            'type' => $this->type,
            'description' => $this->description
        ];

        if ($this->format) {
            $data['format'] = $this->format;
        }

        if ($this->enum) {
            $data['enum'] = $this->enum;
        }

        if ($this->getType() === PropertyType::OBJECT && $this->properties) {
            foreach ($this->formatProperties() as $property) {
                $propObject = $this->createFromArray($property);
                $data['properties'][$propObject->getProperty()] = $propObject->jsonSerialize();
            }
        }

        // TODO: add removeEmptyValues
        return $data;
    }

    private function formatProperties(): array
    {
        $format = [];

        foreach($this->properties as $name => $property) {
            $data =  [
                'property' => $name,
            ];

            if (is_array($property)) {
                $data = array_merge($data, $property);
            } else {
                $data['type'] = $property;
            }

            $format[] = $data;
        }

        return $format;
    }
}
