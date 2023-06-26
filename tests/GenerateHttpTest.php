<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests;

use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\GeneratorHttp;
use OpenApiGenerator\Tests\Examples\Controller\ManyResponsesController;
use OpenApiGenerator\Tests\Examples\Controller\SimpleController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GenerateHttpTest extends TestCase
{
    #[Test]
    public function append(): void
    {
        $dummyReflection = new ReflectionClass(SimpleController::class);

        $generateHttp = new GeneratorHttp();
        $generateHttp->append($dummyReflection);

        $reflection = new ReflectionClass($generateHttp);
        $pathsProperty = $reflection->getProperty('paths');
        $pathsProperty->setAccessible(true);
        $actual = $pathsProperty->getValue($generateHttp);

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
                            "properties": {
                                "prop1": {
                                    "description": "",
                                    "type": "string"
                                }
                            },
                            "type": "object"
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

        $actualJson = json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->assertEquals($expectedJson, $actualJson);
    }

    #[Test]
    public function several_responses(): void
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

        $actual = json_decode(json_encode($actual), true);
        $method = $actual[0]['get'];

        $this->assertArrayHasKey(200, $method['responses']);
        $this->assertArrayHasKey(401, $method['responses']);
    }
}
