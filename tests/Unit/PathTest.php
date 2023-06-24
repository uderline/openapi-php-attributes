<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Unit;

use OpenApiGenerator\Method;
use OpenApiGenerator\Path;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    private Method $method1;

    public function setUp(): void
    {
        parent::setUp();

        $this->method1 = $this->createConfiguredMock(Method::class, [
            'getPath' => '/path/{id}/{otherParameter}',
            'getMethod' => 'GET',
            'jsonSerialize' => []
        ]);
    }

    #[Test]
    public function has_same_path(): void
    {
        $path = new Path($this->method1);

        $method2 = $this->createConfiguredMock(Method::class, [
            'getPath' => '/path/{id}/{otherParameter}',
            'jsonSerialize' => []
        ]);

        $this->assertTrue($path->hasSamePath($method2));
    }

    #[Test]
    public function add_method(): void
    {
        $method2 = $this->createConfiguredMock(Method::class, [
            'getPath' => '/path/{id}/{otherParameter}',
            'getMethod' => 'POST'
        ]);

        $path = new Path($this->method1);
        $path->addMethod($method2);

        $this->assertCount(2, $path->jsonSerialize());
    }

    #[Test]
    public function add_method_throws_an_exception_if_path_is_different(): void
    {
        $method2 = $this->createConfiguredMock(Method::class, [
            'getPath' => '/path/{id}',
            'getMethod' => 'POST'
        ]);

        $path = new Path($this->method1);

        $this->expectException(\InvalidArgumentException::class);
        $path->addMethod($method2);
    }

    #[Test]
    public function testSerialize(): void
    {
        $method2 = $this->createConfiguredMock(Method::class, [
            'getPath' => '/path/{id}/{otherParameter}',
            'getMethod' => 'POST'
        ]);

        $path = new Path($this->method1);
        $path->addMethod($method2);

        $expected = [
            'GET' => [],
            'POST' => []
        ];

        $this->assertEquals($expected, $path->jsonSerialize());
    }
}
