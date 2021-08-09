<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class SecuritySchema implements JsonSerializable
{
    public function __construct(
        private string $securityKey,
        private string $type,
        private string $name,
        private string $in,
        private string $scheme,
        private string $description = '',
        private string $bearerFormat = '',
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $data = [
            'type' => $this->type,
            'name' => $this->name,
            'in' => $this->in,
            'scheme' => $this->scheme,
            'description' => $this->description,
            'bearerFormat' => $this->bearerFormat,
        ];

        return [
            $this->securityKey => removeEmptyValues($data),
        ];
    }
}
