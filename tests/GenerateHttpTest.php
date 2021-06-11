<?php

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Tests\SimpleController;
use PHPUnit\Framework\TestCase;

class GenerateHttpTest extends TestCase
{

    public function testAppend()
    {
        $dummyReflection = new \ReflectionClass(SimpleController::class);

        $generateHttp = new GenerateHttp();
        $generateHttp->append($dummyReflection);

        $reflection = new \ReflectionClass($generateHttp);
        $pathsProperty = $reflection->getProperty("paths");
        $pathsProperty->setAccessible(true);
        $actual = $pathsProperty->getValue($generateHttp);

        $expectedParameter = new Parameter("id", true);
        $expectedParameter->setName("id");
        $expectedParameter->setParamType("integer");
        $expectedRoute = new Route(Route::GET, "/path/{id}", ["Dummy"], "Dummy path");
        $expectedRoute->addParam($expectedParameter);

        self::assertEquals([$expectedRoute], $actual);
    }

    public function testBuild()
    {

    }
}
