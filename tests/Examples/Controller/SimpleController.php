<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Controller;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\SecurityScheme;
use OpenApiGenerator\Attributes\Server;
use OpenApiGenerator\Type;

#[
    Server('same server1', 'same url1'),
    Info("title", "1.0.0", "The description"),
    Server('same server2', 'same url2'),
    SecurityScheme(
        'bearerAuth',
        'http',
        'bearerAuth',
        'header',
        'JWT',
        'bearer',
    ),
    Controller,
]
class SimpleController
{
    #[
        GET("/path/{id}", ["Dummy"], "Dummy path"),
        Property(Type::STRING, "prop1"),
        Response(200),
    ]
    public function get(
        #[Parameter(example: "2")] float $id
    ): void
    {
        //
    }
}
