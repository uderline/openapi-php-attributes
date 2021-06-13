<?php

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
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

        $expectedParameter = new Parameter();
        $expectedParameter->setName("id");
        $expectedParameter->setParamType("integer");

        $expectedRoute = new Route(Route::GET, "/path/{id}", ["Dummy"], "Dummy path");
        $expectedRoute->addParam($expectedParameter);
        $expectedRoute->setRequestBody(new RequestBody());
        $expectedRoute->setResponse(new Response());

        self::assertEquals([$expectedRoute], $actual);
    }

    public function testBuild()
    {
        $this->markTestSkipped("to implement");
    }
}
