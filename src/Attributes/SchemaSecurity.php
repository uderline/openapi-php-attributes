<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class SchemaSecurity implements JsonSerializable
{
    public function __construct(
        private string $schemaKey = '',
        private string $type = '',
        private string $name= '',
        private string $in = '',
        private string $bearerFormat = '',
        private string $scheme = '',
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            $this->schemaKey => [
                'type' => $this->type,
                'name' => $this->name,
                'in' => $this->in,
                'bearerFormat' => $this->bearerFormat,
                'scheme' => $this->scheme,
            ],
        ];
    }
}
