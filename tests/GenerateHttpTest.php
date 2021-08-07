<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\GeneratorHttp;
use OpenApiGenerator\Tests\Examples\Controller\SimpleController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GenerateHttpTest extends TestCase
{
    public function testAppend(): void
    {
        $dummyReflection = new ReflectionClass(SimpleController::class);

        $generateHttp = new GeneratorHttp();
        $generateHttp->append($dummyReflection);

        $reflection = new ReflectionClass($generateHttp);
        $pathsProperty = $reflection->getProperty('paths');
        $pathsProperty->setAccessible(true);
        $actual = $pathsProperty->getValue($generateHttp);

        $expectedParameter = new Parameter(example: '2');
        $expectedParameter->setName('id');
        $expectedParameter->setParamType('float');

        $expectedRoute = new Route(Route::GET, '/path/{id}', ['Dummy'], 'Dummy path');
        $expectedRoute->addParam($expectedParameter);
        $expectedRoute->setRequestBody(new RequestBody());
        $expectedRoute->setResponse(new Response());

        self::assertEquals([$expectedRoute], $actual);
    }

    public function testBuild(): void
    {
        $this->markTestSkipped('to implement');
    }
}
