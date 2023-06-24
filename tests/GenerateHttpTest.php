<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests;

use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\GeneratorHttp;
use OpenApiGenerator\Method;
use OpenApiGenerator\Path;
use OpenApiGenerator\Tests\Examples\Controller\ManyResponsesController;
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

        $expectedMethod = (new Method())
            ->setRoute(new GET('/path/{id}/{otherParameter}', ['Dummy'], 'Dummy path'))
            ->setRequestBody(new RequestBody())
            ->addProperty(new Property('string', 'prop1'))
            ->setResponse(new Response());
        $expectedPath = new Path($expectedMethod);

        $expectedJson = <<<JSON
[
    {
        "get": {
            "tags": [
                "Dummy"
            ],
            "summary": "Dummy path",
            "parameters": [
                {
                    "name": "id",
                    "in": "path",
                    "schema": {
                        "type": "number",
                        "format": "float",
                        "example": "2"
                    },
                    "required": true
                },
                {
                    "name": "otherParameter",
                    "in": "path",
                    "schema": {
                        "type": "string"
                    },
                    "required": true,
                    "description": "Parameter which is not used as an argument in this method"
                }
            ],
            "requestBody": {
                "content": {
                    "application/json": {
                        "schema": {
                            "type": "object",
                            "properties": {
                                "prop1": {
                                    "description": "",
                                    "type": "string"
                                }
                            }
                        }
                    }
                }
            },
            "responses": {
                "200": {
                    "description": ""
                }
            }
        }
    }
]
JSON;

        $actualJson = json_encode($actual, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testManyResponses(): void
    {
        $dummyReflection = new ReflectionClass(ManyResponsesController::class);

        $generateHttp = new GeneratorHttp();
        $generateHttp->append($dummyReflection);

        $reflection = new ReflectionClass($generateHttp);
        $pathsProperty = $reflection->getProperty('paths');
        $pathsProperty->setAccessible(true);
        $actual = $pathsProperty->getValue($generateHttp);

        $expectedRoute = new GET('/path', ['Dummy'], '"Dummy" \path');
        $expectedRoute->setRequestBody(new RequestBody());
        $expectedRoute->addResponse(new Response());
        $expectedRoute->addResponse(new Response(401));

        self::assertEquals([$expectedRoute], $actual);
    }

    public function testBuild(): void
    {
        $this->markTestSkipped('to implement');
    }
}
