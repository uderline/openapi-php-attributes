<?php

namespace OpenApiGenerator\Attributes;

use OpenApiGenerator\Types\SchemaType;
use JsonSerializable;

/**
 * A schema represents a list of properties
 */
#[\Attribute(\Attribute::TARGET_CLASS)] class Schema implements JsonSerializable
{
    private array $properties = [];

    public function __construct(
        private string $schemaType,
        private ?array $required = null,
        private ?string $name = null
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        $array = [
            "type" => $this->schemaType,
        ];

        if ($this->required) {
            $array["required"] = $this->required;
        }

        foreach ($this->properties as $property) {
            if ($this->schemaType === SchemaType::OBJECT) {
                $array["properties"][$property->getProperty()] = $property;
            }
            if ($this->schemaType === SchemaType::ARRAY) {
                $array = [
                    "items" => reset($this->properties)
                ];
            }
        }

        return $array;
    }

    public function addProperty(Property|PropertyItems $property): void
    {
        $this->properties[] = $property;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
