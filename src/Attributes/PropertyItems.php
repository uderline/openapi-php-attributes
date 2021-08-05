<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use OpenApiGenerator\Types\ItemsType;
use JsonSerializable;

/**
 * Describe items of a property (an array)
 * If the array is an array of components, the ref argument can be set along with the type being an object
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class PropertyItems implements PropertyInterface, JsonSerializable
{
    private mixed $example = "";

    public function __construct(
        private string $type,
        private ?string $ref = null
    ) {
        if ($this->ref) {
            $ref = explode('\\', $this->ref);
            $this->ref = end($ref);
        }
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
        $array = [
            "items" => []
        ];

        if ($this->type === ItemsType::REF && $this->ref) {
            $array["items"] = ['$ref' => "#/components/schemas/$this->ref"];
        } else {
            $array["items"] = [
                "type" => $this->type,
                "example" => $this->example
            ];
        }

        return $array;
    }
}
