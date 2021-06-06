<?php

namespace OpenApiGenerator\Attributes;

use OpenApiGenerator\Types\ItemsType;
use JsonSerializable;

/**
 * Describe items of a property (an array)
 * If the array is an array of components, the ref argument can be set along with the type being an object
 */
#[\Attribute(\Attribute::IS_REPEATABLE|\Attribute::TARGET_ALL)]
class PropertyItems implements JsonSerializable
{
    private mixed $example = "";

    public function __construct(private string $type, private ?string $ref = null)
    {
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
        if ($this->type === ItemsType::REF) {
            $ref = explode('\\', $this->ref);
            $ref = last($ref);

            return ['$ref' => "#/components/schemas/$ref"];
        }

        return [
            "type" => $this->type,
            "example" => $this->example
        ];
    }
}
