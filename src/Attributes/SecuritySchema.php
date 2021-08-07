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
        $securityScheme = [
            $this->securityKey => [
                'type' => $this->type,
                'name' => $this->name,
                'in' => $this->in,
                'scheme' => $this->scheme,
            ],
        ];

        $securitySchemeKey = &$securityScheme[$this->securityKey];
        if ($this->description) {
            $securitySchemeKey['description'] = $this->description;
        }

        if ($this->bearerFormat) {
            $securitySchemeKey['bearerFormat'] = $this->bearerFormat;
        }

        return $securityScheme;
    }
}
