<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute]
class POST extends Route {
    #[Pure]
    public function __construct(string $route, array $tags = [], string $summary = '')
    {
        parent::__construct(self::POST, $route, $tags, $summary);
    }
}
