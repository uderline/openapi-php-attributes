<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\SchemaType;

#[
    Schema(SchemaType::OBJECT),
    Property(PropertyType::STRING, "prop1", "Prop1 description", "Value_1", isObjectId: true),
    Property(PropertyType::INT, "prop2", example: "Value2"),
]
class DummyRefComponent
{
    public string $prop1;
    public int $prop2;
}
