<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

interface PropertyInterface
{
    public function getType(): string;
}
