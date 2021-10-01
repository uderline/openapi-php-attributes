<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute]
class DELETE extends Route {
    #[Pure]
    public function __construct(string $route, array $tags = [], string $summary = '')
    {
        parent::__construct(self::DELETE, $route, $tags, $summary);
    }
}
