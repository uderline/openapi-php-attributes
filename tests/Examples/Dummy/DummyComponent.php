<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;

#[
    Schema(SchemaType::OBJECT),
    Property(Type::STRING, "prop1", "Prop1 description", "Value 1"),
    Property(Type::INT, "prop2", example: "Value2"),
    Property(Type::BOOLEAN, "prop3", "Prop 3"),
    Property(Type::STRING, "prop4", "Prop 4", enum: ["val1", "val2"]),
    Property(Type::STRING, "prop5", "Prop 4", enum: DummyBackedEnum::class),
    Property(Type::ARRAY, "propArray"),
    PropertyItems(Type::REF, DummyRefComponent::class)
]
class DummyComponent
{
    public string $prop1;
    public int $prop2;
    public bool $prop3;
    public string $prop4;
}
