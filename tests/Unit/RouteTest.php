<?php

declare(strict_types=1);

namespace Unit;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    #[Test]
    public function json_returns_a_simple_route(): void
    {
        $request = new RequestBody();
        $request->addProperty(new Property('test', 'string'));

        $route = new Route('GET', '/test', ['tag1'], 'summary1');
        $route->setRequestBody($request);
        $route->addResponse(new Response());
        $route->setParameters([new Parameter('description', 'path')]);

        $json = $route->jsonSerialize();

        $this->assertEquals(['tag1'], $json['tags']);
        $this->assertEquals("summary1", $json['summary']);
        $this->assertInstanceOf(RequestBody::class, $json['requestBody']);
        $this->assertIsArray($json['parameters']);
        $this->assertIsArray($json['responses']);
    }

    #[Test]
    public function get_path_must_start_with_a_slash(): void
    {
        $route = new Route('GET', 'test');

        $this->assertEquals('/test', $route->getRoute());
    }

    #[Test]
    public function set_parameters_should_automatically_add_parameters(): void
    {
        $route = new Route('GET', '/route/{route}');
        $json = $route->jsonSerialize();

        $this->assertEquals('route', $json['parameters'][0]->getName());
    }
}