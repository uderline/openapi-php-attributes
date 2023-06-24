<?php

declare(strict_types=1);

namespace Unit;

use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RefProperty;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\IllegalFieldException;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\MediaType;
use OpenApiGenerator\Types\SchemaType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class ParameterTest extends TestCase
{
    #[Test, DataProvider('setParamTypeProvider')]
    public function set_param_type_should_return_an_array_based_on_the_string_type(string $type, array $expected): void
    {
        $parameter = new Parameter();
        $parameter->setName('prop');
        $parameter->setParamType($type);
        $json = $parameter->jsonSerialize();

        $this->assertEquals($expected, $json['schema']);
    }

    public static function setParamTypeProvider(): array
    {
        return [
            ['type' => 'int', 'expected' => ['type' => 'integer']],
            ['type' => 'bool', 'expected' => ['type' => 'boolean']],
            ['type' => 'float', 'expected' => ['type' => 'number', 'format' => 'float']],
            ['type' => 'double', 'expected' => ['type' => 'number', 'format' => 'double']],
            ['type' => 'mixed', 'expected' => []],
        ];
    }

    #[Test]
    public function json_should_throw_an_exception_if_name_is_null(): void
    {
        $parameter = new Parameter();

        $this->expectException(IllegalFieldException::class);
        $parameter->jsonSerialize();
    }

    #[Test]
    public function json_should_contain_required_key_if_its_true(): void
    {
        $parameter = (new Parameter(required: true))
            ->setName('prop')
            ->setParamType('string');

        $this->assertArrayHasKey('required', $parameter->jsonSerialize());
    }

    #[Test]
    public function json_should_not_contain_required_key_if_its_false_and_not_in_path(): void
    {
        $parameter = (new Parameter(in: 'query', required: false))
            ->setName('prop')
            ->setParamType('string');

        $this->assertArrayNotHasKey('required', $parameter->jsonSerialize());
    }

    #[Test]
    public function json_should_contain_required_key_event_if_its_false_and_in_path(): void
    {
        $parameter = (new Parameter(required: false))
            ->setName('prop')
            ->setParamType('string');

        $this->assertTrue($parameter->jsonSerialize()['required']);
    }

    #[Test]
    public function json_should_contain_description_key_if_its_not_null(): void
    {
        $parameter = (new Parameter(description: 'description'))
            ->setName('prop')
            ->setParamType('string');

        $this->assertArrayHasKey('description', $parameter->jsonSerialize());
    }

    #[Test]
    public function json_should_contain_example_key_if_its_not_null(): void
    {
        $parameter = (new Parameter(example: 'example'))
            ->setName('prop')
            ->setParamType('string');

        $this->assertArrayHasKey('example', $parameter->jsonSerialize()['schema']);
    }

    #[Test]
    public function json_should_contain_format_key_if_its_not_null(): void
    {
        $parameter = (new Parameter(format: 'date'))
            ->setName('prop')
            ->setParamType('string');

        $this->assertArrayHasKey('format', $parameter->jsonSerialize()['schema']);
    }
}