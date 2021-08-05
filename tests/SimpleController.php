<?php

namespace OpenApiGenerator\Tests;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;

#[Info("title", "1.0.0")]
#[Controller]
class SimpleController
{
    #[
        Route(Route::GET, "/path/{id}", ["Dummy"], "Dummy path"),
        Response
    ]
    public function get(#[Parameter] int $id): void
    {
        //
    }
}
