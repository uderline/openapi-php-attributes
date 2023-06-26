<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;

#[Attribute]
class PUT extends Route
{
    public function __construct(string $route, array $tags = [], string $summary = '')
    {
        parent::__construct(self::PUT, $route, $tags, $summary);
    }
}
