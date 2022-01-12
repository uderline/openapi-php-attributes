<?php

namespace OpenApiGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class PathParameter extends Parameter
{
    public function __construct(
        string  $name,
        string  $type = 'string',
        ?string $description = null,
        string  $in = 'path',
        ?bool   $required = null,
        mixed   $example = '',
        mixed   $format = ''
    )
    {
        parent::__construct($description, $in, $required, $example, $format);
        $this->setName($name);
        $this->setParamType($type);
    }
}
