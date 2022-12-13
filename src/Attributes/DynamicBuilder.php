<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute]
class DynamicBuilder {
    public static function getBuilder() {
        // Make instance of external resolver and pass all the required parameters
    }
}
