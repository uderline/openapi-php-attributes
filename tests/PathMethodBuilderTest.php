<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests;

use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\PathMethodBuilder;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\RequestBodyType;
use OpenApiGenerator\Types\SchemaType;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PathMethodBuilderTest extends TestCase
{
    public function testAddResponse(): void
    {
        $response = new Response();

        $builder = new PathMethodBuilder();
        $builder->setResponse($response);

        $reflection = new ReflectionClass($builder);
        $responseProperty = $reflection->getProperty("currentSchemaHolder");
        $responseProperty->setAccessible(true);

        self::assertEquals($response, $responseProperty->getValue($builder));
    }

    /**
     * @requires testSetRoute
     */
    public function testGetRouteWithoutSavingSchemaHolder(): void
    {
        $route = new Route(Route::GET, "/path");

        $builder = new PathMethodBuilder();

        // Use a reflection to avoid testing PathMethodBuilder::setRoute
        $reflection = new ReflectionClass($builder);
        $property = $reflection->getProperty("currentRoute");
        $property->setAccessible(true);
        $property->setValue($builder, $route);

        self::assertEquals($route, $builder->getRoute());
    }


    /**
     * The first possibility is adding a property as an object
     *
     * @requires testSetRoute
     * @requires testGetRoute
     * @requires testSetRequestBody
     */
    public function testAddPropertyObject(): void
    {
        $route = new Route(Route::POST, "/path");
        $property = new Property(PropertyType::INT, "prop1");

        $builder = new PathMethodBuilder();
        $builder->setRoute($route, []);
        $builder->setRequestBody(new RequestBody(RequestBodyType::JSON));
        $builder->addProperty($property);

        // Manually make the route
        $schema = new Schema(SchemaType::OBJECT);
        $schema->addProperty($property);
        $requestBody = new RequestBody(RequestBodyType::JSON);
        $requestBody->setSchema($schema);
        $expectedRoute = new Route(Route::POST, "/path");
        $expectedRoute->setRequestBody($requestBody);

        self::assertEquals($expectedRoute, $builder->getRoute());
    }

    /**
     * The other possibility is to add a property as an array
     *
     * @requires testGetRoute
     */
    public function testAddPropertyArray(): void
    {
        $route = new Route(Route::POST, "/path");
        $property = new Property(PropertyType::ARRAY, "prop1");

        $builder = new PathMethodBuilder();
        $builder->setRoute($route, []);
        $builder->setRequestBody(new RequestBody(RequestBodyType::JSON));
        $builder->addProperty($property);

        // Manually make the route
        $schema = new Schema(SchemaType::ARRAY);
        $schema->addProperty($property);
        $requestBody = new RequestBody(RequestBodyType::JSON);
        $requestBody->setSchema($schema);
        $expectedRoute = new Route(Route::POST, "/path");
        $expectedRoute->setRequestBody($requestBody);

        self::assertEquals($expectedRoute, $builder->getRoute());
    }

    /**
     * @requires testAddPropertyArray
     * @requires testGetRoute
     */
    public function testAddPropertyItems(): void
    {
        $route = new Route(Route::POST, "/path");
        $property = new Property(PropertyType::ARRAY, "prop1");

        $builder = new PathMethodBuilder();
        $builder->setRoute($route, []);
        $builder->setRequestBody(new RequestBody());
        $builder->addProperty($property);
        $builder->setPropertyItems(new PropertyItems(PropertyType::INT));
        $route = $builder->getRoute();

        // Access the property items of the route to check it has been set
        $reflection = new ReflectionClass($route);
        $requestBodyProperty = $reflection->getProperty("requestBody");
        $requestBodyProperty->setAccessible(true);
        $requestBodyPropertyValue = $requestBodyProperty->getValue($route);
        $reflection = new ReflectionClass($requestBodyPropertyValue);
        $schemaProperty = $reflection->getProperty("schema");
        $schemaProperty->setAccessible(true);

        $expectedArray = [
            "application/json" => [
                "schema" => [
                    "type" => "array",
                    "items" => [
                        "type" => PropertyType::INT,
                        "example" => ""
                    ]
                ]
            ]
        ];

        $actualArray = json_decode(json_encode($schemaProperty->getValue($requestBodyPropertyValue)), true);


        self::assertEquals($expectedArray, $actualArray);
    }

    public function testSetRoute(): void
    {
        $route = new Route(Route::GET, "/path");

        $builder = new PathMethodBuilder();
        $builder->setRoute($route, [new Parameter("desc")]);

        // Use a reflection to avoid testing PathMethodBuilder::getRoute
        $reflection = new ReflectionClass($builder);
        $property = $reflection->getProperty("currentRoute");
        $property->setAccessible(true);

        self::assertEquals($route, $property->getValue($builder));
    }

    public function testSetRequestBody(): void
    {
        $requestBody = new RequestBody();

        $builder = new PathMethodBuilder();
        $builder->setRequestBody($requestBody);

        $reflection = new ReflectionClass($builder);
        $schemaProperty = $reflection->getProperty("currentSchemaHolder");
        $schemaProperty->setAccessible(true);

        self::assertEquals($requestBody, $schemaProperty->getValue($builder));
    }
}
