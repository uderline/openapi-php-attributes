<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\IDParam;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\POST;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\MediaType;
use OpenApiGenerator\Types\SchemaType;

#[Info("title", "1.0.0")]
#[Controller]
class DummyController
{
    #[
        GET("/path", ["Dummy"], "Dummy get path"),
        Property(Type::STRING, "prop1", "Prop 1", "val1"),
        Property(Type::STRING, "prop2", "Prop 2", "val2"),
        Response
    ]
    public function get(): void
    {
        //
    }

    #[
        GET("/path/entity/{id}", ["Dummy"], "Get an entity with a ref"),
        Response(ref: DummyComponent::class)
    ]
    public function getEntity(#[IDParam] int $id): void
    {
        //
    }

    #[
        GET("/path/entities", ["Dummy"], "Get a list"),
        Response(schemaType: SchemaType::ARRAY, ref: DummyComponent::class)
    ]
    public function getEntities(): void
    {
        //
    }

    #[
        GET("/path/image/{prop1}", ["Dummy"], "Dummy image path"),
        Response,
        MediaProperty(MediaType::MEDIA_IMAGE_PNG, MediaType::ENCODING_BASE64)
    ]
    public function getImage(#[Parameter("prop1")] int $prop1): void
    {
        //
    }

    #[
        POST("/path", ["Dummy"], "Dummy post"),
        Property(Type::STRING, "prop1"),
        Property(Type::STRING, "prop2", "Prop2 description"),
        Property(Type::STRING, "prop3", "Prop3 description", "Value 3 example"),
        Response(201)
    ]
    public function post(): void
    {
        //
    }

    #[
        PUT("/path/{id}", ["Dummy"], "Dummy put"),
        Property(Type::STRING, "prop1"),
        Property(Type::ID, "prop2"),
        Property(Type::BOOLEAN, "prop3"),
        Response(204)
    ]
    public function put(#[IDParam] int $id): void
    {
        //
    }
}
