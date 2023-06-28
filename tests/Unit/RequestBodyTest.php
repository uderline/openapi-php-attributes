<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Unit;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RequestBodyTest extends TestCase
{
    #[Test]
    public function adding_property_may_create_a_schema_if_it_doesnt_exist(): void
    {
        $requestBody = new RequestBody();
        $requestBody->addProperty(new Property(Type::INT, "prop"));

        $this->assertArrayHasKey('content', $requestBody->jsonSerialize());
    }

    #[Test]
    public function is_empty_will_return_true_if_no_schema_exist(): void
    {
        $requestBody = new RequestBody();

        $this->assertTrue($requestBody->isEmpty());
    }

    #[Test]
    public function is_empty_will_return_false_if_a_schema_exist(): void
    {
        $requestBody = new RequestBody();
        $requestBody->setSchema(new Schema());

        $this->assertFalse($requestBody->isEmpty());
    }

    #[Test]
    public function json_will_return_an_empty_array_if_the_body_is_empty(): void
    {
        $requestBody = new RequestBody();

        $this->assertEmpty($requestBody->jsonSerialize());
    }
}