<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;

#[Attribute]
class GET extends Route
{
    public function __construct(string $route, array $tags = [], string $summary = '')
    {
        parent::__construct(self::GET, $route, $tags, $summary);
    }
}
