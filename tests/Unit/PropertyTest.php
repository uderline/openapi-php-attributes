<?php

declare(strict_types=1);

namespace Unit;

use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\IllegalFieldException;
use OpenApiGenerator\Tests\Examples\Dummy\DummyBackedEnum;
use OpenApiGenerator\Tests\Examples\Dummy\DummyRefComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    #[Test]
    public function json_must_return_the_property(): void
    {
        $schema = new Property("string", "title", "description", "example", "format", ["enum"], null, false, [], false);

        $this->assertEqualsCanonicalizing([
            'type' => 'string',
            'description' => 'description',
            'example' => 'example',
            'format' => 'format',
            'enum' => ["enum"],
        ], $schema->jsonSerialize());
    }

    #[Test]
    public function json_must_return_the_property_with_propertyItems(): void
    {
        $propertyItems = new PropertyItems("string", "title", "description");
        $property = new Property("array", "prop");
        $property->setPropertyItems($propertyItems);

        $this->assertEqualsCanonicalizing([
            'type' => 'array',
            'items' => [
                'type' => 'string',
            ]
        ], $property->jsonSerialize());
    }

    #[Test]
    public function json_must_throw_an_exception_when_propertyItems_is_not_set(): void
    {
        $property = new Property("array", "prop");

        $this->expectException(IllegalFieldException::class);
        $property->jsonSerialize();
    }

    #[Test]
    public function json_must_return_the_property_with_a_ref(): void
    {
        $property = new Property("ref", "prop", ref: DummyRefComponent::class);

        $this->assertEqualsCanonicalizing([
            '$ref' => '#/components/schemas/DummyRefComponent'
        ], $property->jsonSerialize());
    }
    
    #[Test]
    public function json_must_return_the_property_with_a_nullable_ref(): void
    {
        $property = new Property("ref", "prop", ref: DummyRefComponent::class, nullable: true);

        $this->assertEqualsCanonicalizing([
            'anyOf' => [
                ['type' => 'null'],
                ['$ref' => '#/components/schemas/DummyRefComponent'],
            ]
        ], $property->jsonSerialize());
    }

    #[Test]
    public function json_must_set_integer_type_and_a_min_value_for_id_type(): void
    {
        $property = new Property("id", "prop");

        $this->assertEqualsCanonicalizing([
            'description' => '',
            'type' => 'integer',
            'minimum' => 1,
        ], $property->jsonSerialize());
    }

    #[Test]
    public function json_must_return_the_property_with_a_nullable_type(): void
    {
        $property = new Property("string", "prop", nullable: true);

        $this->assertEqualsCanonicalizing([
            'description' => '',
            'anyOf' => [
                ['type' => 'string'],
                ['type' => 'null'],
            ]
        ], $property->jsonSerialize());
    }

    #[Test]
    public function json_must_return_an_enum_from_an_enum(): void
    {
        $property = new Property("string", "prop", enum: DummyBackedEnum::class);

        $this->assertEqualsCanonicalizing([
            'description' => '',
            'type' => 'string',
            'enum' => ['value1', 'value2'],
        ], $property->jsonSerialize());
    }
}