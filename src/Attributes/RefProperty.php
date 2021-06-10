<?php

namespace OpenApiGenerator\Attributes;

use OpenApiGenerator\Types\MediaType;
use OpenApiGenerator\Types\PropertyType;
use JsonSerializable;
use OpenApiGenerator\Types\SchemaType;

/**
 * This represents an open api property.
 * The property must have a type and a property name and can have a description and an example
 * If the property is an array, a PropertyItems must be set
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_ALL)]
class RefProperty implements PropertyInterface, JsonSerializable
{
    public function __construct(
        private string $ref,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return ['$ref' => "#/components/schemas/$this->ref"];
    }

    public function getType(): string
    {
        return PropertyType::REF;
    }
}
