<?php

declare(strict_types=1);

namespace OpenApiGenerator\Types;

class PropertyType
{
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const INT = 'integer';
    public const REF = 'ref';
    public const BOOLEAN = 'boolean';
    public const OBJECT = 'object';
    public const MEDIA = 'media';
}
