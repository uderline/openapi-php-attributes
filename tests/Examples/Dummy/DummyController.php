<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Types\MediaType;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\SchemaType;

#[Info("title", "1.0.0")]
#[Controller]
class DummyController
{
    #[
        Route(Route::GET, "/path", ["Dummy"], "Dummy get path"),
        Response,
        Property(PropertyType::STRING, "prop1", "Prop 1", "val1"),
        Property(PropertyType::STRING, "prop2", "Prop 2", "val2")
    ]
    public function get(): void
    {
        //
    }

    #[
        Route(Route::GET, "/path/entity/{id}", ["Dummy"], "Get an entity with a ref"),
        Response(ref: DummyComponent::class)
    ]
    public function getEntity(#[Parameter] int $id): void
    {
        //
    }

    #[
        Route(Route::GET, "/path/entities", ["Dummy"], "Get a list"),
        Response(schemaType: SchemaType::ARRAY, ref: DummyComponent::class)
    ]
    public function getEntities(): void
    {
        //
    }

    #[
        Route(Route::GET, "/path/image/{prop1}", ["Dummy"], "Dummy image path"),
        Response,
        MediaProperty(MediaType::MEDIA_IMAGE_PNG, MediaType::ENCODING_BASE64)
    ]
    public function getImage(#[Parameter("prop1")] int $prop1): void
    {
        //
    }

    #[
        Route(Route::POST, "/path", ["Dummy"], "Dummy post"),
        Property(PropertyType::STRING, "prop1"),
        Property(PropertyType::STRING, "prop2", "Prop2 description"),
        Property(PropertyType::STRING, "prop3", "Prop3 description", "Value 3 example"),
        Response(201)
    ]
    public function post(): void
    {
        //
    }

    #[
        Route(Route::PUT, "/path/{id}", ["Dummy"], "Dummy put"),
        Property(PropertyType::STRING, "prop1"),
        Property(PropertyType::INT, "prop2"),
        Property(PropertyType::BOOLEAN, "prop3"),
        Response(204)
    ]
    public function put(#[Parameter] int $id): void
    {
        //
    }
}
