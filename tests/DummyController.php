<?php

namespace OpenApiGenerator\Tests;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\MediaProperty;
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Types\MediaType;

#[Info("title", "1.0.0")]
#[Controller]
class DummyController
{
    #[
        Route(Route::GET, "/path/{prop1}", ["Dummy"], "Dummy path"),
        Response,
        MediaProperty(MediaType::MEDIA_IMAGE_PNG, MediaType::ENCODING_BASE64)
    ]
    public function get(#[Parameter("prop1")] int $prop1): void
    {

    }
}