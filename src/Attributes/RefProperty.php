<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use OpenApiGenerator\Types\PropertyType;

/**
 * This represents an open api property.
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class RefProperty implements PropertyInterface, JsonSerializable
{
    public function __construct(
        private string $ref,
    ) {
        $ref = explode('\\', $this->ref);
        $this->ref = end($ref);
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
