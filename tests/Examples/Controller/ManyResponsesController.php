<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Controller;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;

#[Controller]
class ManyResponsesController
{
    #[
        GET("/path", ["Dummy"], '"Dummy" \path'),
        Response(200),
        Response(401),
    ]
    public function get(): void
    {
        //
    }
}
