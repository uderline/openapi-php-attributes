<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Controller;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Attributes\SecuritySchema;
use OpenApiGenerator\Attributes\Server;
use OpenApiGenerator\Types\SchemaType;

#[
    Server('same server1', 'same url1'),
    Info(
        'title',
        '1.0.0',
        'The summary',
        'description',
        'url terms Of Service',
        [
            'name' => 'API Support',
            'url' => 'https://www.example.com/support',
            'email' => 'support@example.com'
        ],
        [
            'name' => 'Apache 2.0',
            'url' => 'https://www.apache.org/licenses/LICENSE-2.0.html'
        ],
    ),
    Server('same server2', 'same url2'),
    SecuritySchema(
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
        Route(Route::GET, '/path/{id}', ['Dummy'], 'Dummy path'),
        Response(200),
    ]
    public function get(
        #[Parameter(example: '2')] float $id
    ): void {
        //
    }
}
