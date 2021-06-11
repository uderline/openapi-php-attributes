<?php

namespace OpenApiGenerator\Attributes;

use OpenApiGenerator\Types\ItemsType;
use JsonSerializable;
use OpenApiGenerator\Types\PropertyType;

/**
 * Describe items of a property (an array)
 * If the array is an array of components, the ref argument can be set along with the type being an object
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_ALL)]
class PropertyItems implements PropertyInterface, JsonSerializable
{
    private mixed $example = "";

    public function __construct(private string $type, private ?string $ref = null)
    {
        $ref = explode('\\', $this->ref);
        $this->ref = end($ref);
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function setExample(mixed $example): void
    {
        $this->example = $example;
    }

    public function jsonSerialize(): array
    {
        if ($this->type === ItemsType::REF && $this->ref) {
            return [
                "items" => [
                    '$ref' => "#/components/schemas/$this->ref"
                ]
            ];
        }

        return [
            "type" => $this->type,
            "example" => $this->example
        ];
    }
}
