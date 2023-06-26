<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Unit;

use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RefProperty;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\MediaType;
use OpenApiGenerator\Types\SchemaType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class SchemaTest extends TestCase
{
    #[Test]
    public function get_media_type_returns_json_by_default(): void
    {
        $schema = new Schema();
        $jsonKeys = array_keys($schema->jsonSerialize());
        $mediaType = reset($jsonKeys);

        $this->assertEquals("application/json", $mediaType);
    }

    #[Test]
    public function get_media_type_returns_plain_text_if_schema_is_string(): void
    {
        $schema = new Schema();
        $schema->setSchemaType(SchemaType::STRING);
        $jsonKeys = array_keys($schema->jsonSerialize());
        $mediaType = reset($jsonKeys);

        $this->assertEquals("text/plain", $mediaType);
    }

    #[Test]
    public function get_media_type_returns_media_property_type(): void
    {
        $schema = new Schema();
        $schema->addProperty(new MediaProperty(MediaType::MEDIA_IMAGE_PNG, MediaType::ENCODING_BASE64));
        $jsonKeys = array_keys($schema->jsonSerialize());
        $mediaType = reset($jsonKeys);

        $this->assertEquals("image/png", $mediaType);
    }

    #[Test]
    public function json_should_be_an_array_type_when_it_contains_a_property_items(): void
    {
        $schema = new Schema();
        $schema->addProperty(new PropertyItems(Type::STRING));
        $json = $schema->jsonSerialize();
        $schema = reset($json)['schema'];

        $this->assertEquals("array", $schema['type']);
    }

    #[Test]
    public function json_with_only_one_property_items_will_always_override_the_schema_type_to_array(): void
    {
        $schema = new Schema();
        $schema->setSchemaType(null);
        $schema->addProperty(new PropertyItems(Type::STRING));
        $json = $schema->jsonSerialize();

        $this->assertEquals("array", $json['application/json']['schema']['type']);
    }

    #[Test]
    public function json_with_array_schema_should_not_return_properties(): void
    {
        $schema = new Schema();
        $schema->addProperty(new PropertyItems(Type::STRING));
        $json = $schema->jsonSerialize();

        $this->assertArrayNotHasKey("properties", $json['application/json']['schema']);
    }

    #[Test]
    public function json_contains_a_ref_property_without_a_type(): void
    {
        $schema = new Schema();
        $schema->addProperty(new RefProperty(StdClass::class));
        $json = $schema->jsonSerialize();
        $schema = reset($json)['schema'];

        $this->assertIsArray($schema);
        $this->assertArrayNotHasKey("type", $schema);
    }

    #[Test]
    public function json_contains_properties(): void
    {
        $schema = new Schema();
        $schema->addProperty(new Property(Type::STRING, "prop_string"));
        $json = $schema->jsonSerialize();
        $schema = reset($json)['schema'];

        $this->assertArrayHasKey("properties", $schema);
    }

    #[Test]
    public function json_contains_a_property(): void
    {
        $schema = new Schema();
        $schema->addProperty(new Property(Type::STRING, "prop_string"));
        $json = $schema->jsonSerialize();
        $schema = reset($json)['schema'];

        $this->assertInstanceOf(PropertyInterface::class, $schema['properties']['prop_string']);
    }

    #[Test]
    public function json_will_not_return_media_type_if_no_media_is_true(): void
    {
        $schema = new Schema();
        $schema->setNoMedia(true);
        $schema->addProperty(new Property(Type::INT, "prop"));
        $json = json_decode(json_encode($schema), true);

        $this->assertArrayNotHasKey("application/json", $json);
    }

    #[Test]
    public function json_will_contain_a_required_field_if_specified(): void
    {
        $schema = new Schema(['prop']);
        $schema->addProperty(new Property(Type::INT, "prop"));
        $json = json_decode(json_encode($schema), true);

        $this->assertEquals(['prop'], $json['application/json']['schema']['required']);
    }
}