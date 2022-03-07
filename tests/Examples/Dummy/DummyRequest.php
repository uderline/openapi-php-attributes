<?php

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use Symfony\Component\HttpFoundation\Request;

#[
    Schema,
    Property(Type::STRING, "Property 1"),
    Property(Type::INT, "Property 2"),
    Property(Type::ARRAY, "Property 3"),
    PropertyItems(Type::STRING),
    Property(Type::REF, "Property 4", ref: DummyRefComponent::class),
]
class DummyRequest extends Request
{

}