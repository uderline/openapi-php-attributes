<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\SchemaType;

#[
    Schema(SchemaType::OBJECT),
    Property(PropertyType::STRING, "prop1", "Prop1 description", "Value 1"),
    Property(PropertyType::INT, "prop2", example: "Value2"),
    Property(PropertyType::BOOLEAN, "prop3", "Prop 3"),
    Property(PropertyType::STRING, "prop4", "Prop 4", enum: ["val1", "val2"]),
    Property(PropertyType::ARRAY, "propArray"),
    PropertyItems(PropertyType::REF, DummyRefComponent::class)
]
class DummyComponent
{
    public string $prop1;
    public int $prop2;
    public bool $prop3;
    public string $prop4;
}
