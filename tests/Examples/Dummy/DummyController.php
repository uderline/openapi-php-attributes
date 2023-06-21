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
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\PUT;
use OpenApiGenerator\Attributes\Response;
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
        Response,
        Property(Type::STRING, "Response prop 1", "Prop response 1", "1"),
        Property(Type::INT, "Response prop 2", "Prop response 2", 4)
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
        Property(Type::STRING, "prop2", "Prop2 description", nullable: true),
        Property(Type::STRING, "prop3", "Prop3 description", "Value 3 example"),
        Property(Type::ARRAY, "prop4", "Prop4 description"),
        PropertyItems(Type::STRING),
        Response(201, schemaType: SchemaType::STRING)
    ]
    public function post(): void
    {
        //
    }

    #[
        PUT("/path/{id}", ["Dummy"], "Dummy put"),
        Response(200, ref: DummyRefComponent::class)
    ]
    public function put(#[IDParam] DummyRefComponent $id, DummyRequest $dummyRequest): void
    {
        //
    }
}
