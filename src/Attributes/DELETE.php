<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;

#[Attribute]
class DELETE extends Route
{
    public function __construct(string $route, array $tags = [], string $summary = '')
    {
        parent::__construct(self::DELETE, $route, $tags, $summary);
    }
}
