<?php

declare(strict_types=1);

namespace Unit;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\IllegalFieldException;
use OpenApiGenerator\SchemaBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class SchemaBuilderTest extends TestCase
{
    #[Test]
    public function schema_can_have_no_media_in_schema(): void
    {
        $schemaBuilder = new SchemaBuilder(true);
        $schemaBuilder->addSchema(new Schema(), stdClass::class);
        $schemaBuilder->addProperty(new Property("string", "prop1"));
        $json = json_decode(json_encode($schemaBuilder->getComponent()), true);

        $this->assertArrayHasKey("schema", $json);
    }

    #[Test]
    public function schema_can_have_media_in_schema(): void
    {
        $schemaBuilder = new SchemaBuilder(false);
        $schemaBuilder->addSchema(new Schema(), stdClass::class);
        $schemaBuilder->addProperty(new Property("string", "prop1"));
        $component = $schemaBuilder->getComponent();

        $this->assertArrayHasKey("application/json", $component->jsonSerialize());
    }

    #[Test]
    public function adding_a_property_without_setting_a_schema_throws_an_exception(): void
    {
        $this->expectException(IllegalFieldException::class);

        $schemaBuilder = new SchemaBuilder(true);
        $schemaBuilder->addProperty(new Property("string", "prop1"));
    }

    #[Test]
    public function adding_a_property_items_without_setting_a_property_throws_an_exception(): void
    {
        $this->expectException(IllegalFieldException::class);

        $schemaBuilder = new SchemaBuilder(true);
        $schemaBuilder->addSchema(new Schema(), stdClass::class);
        $schemaBuilder->addPropertyItems(new PropertyItems("string"));
    }
}