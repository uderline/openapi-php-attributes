<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute]
class GET extends Route {
    #[Pure]
    public function __construct(string $route, array $tags = [], string $summary = '')
    {
        parent::__construct(self::GET, $route, $tags, $summary);
    }
}
