<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Controller;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Attributes\SecurityScheme;
use OpenApiGenerator\Attributes\Server;
use OpenApiGenerator\Types\PropertyType;

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
        Route(Route::GET, "/path/{id}", ["Dummy"], "Dummy path"),
        Property(PropertyType::OBJECT, 'test', properties: [
            'data' => PropertyType::STRING,
            'item' => [
                'type' => PropertyType::STRING,
            ],
            'anotherObject' => [
                'type' => PropertyType::OBJECT,
                'properties' => [
                    'example' => PropertyType::STRING,
                ],
            ],
        ]),
        Response(200),
    ]
    public function get(
        #[Parameter(example: "2")] float $id
    ): void
    {
        //
    }
}
