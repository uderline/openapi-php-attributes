<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class DynamicBuilder
{
    public function __construct(public readonly string $builder)
    {
        if (!class_exists($builder)) {
            throw new \InvalidArgumentException("Builder class {$builder} does not exist");
        }
    }
}
