<?php

declare(strict_types=1);

namespace Unit;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Tests\Examples\Dummy\DummyRefComponent;
use OpenApiGenerator\Type;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    #[Test]
    public function json_returns_a_simple_response_with_default_values(): void
    {
        $response = new Response();
        $json = $response->jsonSerialize();
        $keys = array_keys($json);
        $code = reset($keys);

        $this->assertEquals(200, $code);
        $this->assertEqualsCanonicalizing([[
            "description" => "",
        ]], $json);
    }

    #[Test]
    public function create_response_with_a_ref(): void
    {
        $response = new Response(ref: DummyRefComponent::class);

        $this->assertCount(1, $response->jsonSerialize()[200]['content']);
    }

    #[Test]
    public function create_response_with_a_ref_and_a_schema_type(): void
    {
        $response = new Response(schemaType: Type::ARRAY, ref: DummyRefComponent::class);

        $this->assertCount(1, $response->jsonSerialize()[200]['content']);
    }

    #[Test]
    public function json_may_return_response_with_extra(): void
    {
        $response = new Response(extra: ['foo' => 'bar']);
        $json = $response->jsonSerialize();
        $keys = array_keys($json);
        $code = reset($keys);

        $this->assertEquals(200, $code);
        $this->assertEqualsCanonicalizing([[
            "description" => "",
            "foo" => "bar"
        ]], $json);
    }
}