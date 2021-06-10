<?php

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Tests\DummyController;
use PHPUnit\Framework\TestCase;

class GenerateHttpTest extends TestCase
{

    public function testAppend()
    {
        $dummyReflection = new \ReflectionClass(DummyController::class);

        $generateHttp = new GenerateHttp();
        $generateHttp->append($dummyReflection);

        $reflection = new \ReflectionClass($generateHttp);
        $pathsProperty = $reflection->getProperty("paths");
        $pathsProperty->setAccessible(true);
        $actual = $pathsProperty->getValue($generateHttp);

        $expectedParameter = new Parameter("prop1", true);
        $expectedParameter->setName("prop1");
        $expectedParameter->setParamType("integer");
        $expectedRoute = new Route(Route::GET, "/path/{prop1}", ["Dummy"], "Dummy path");
        $expectedRoute->addParam($expectedParameter);

        self::assertEquals([$expectedRoute], $actual);
    }

    public function testBuild()
    {

    }
}
