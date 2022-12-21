<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use Symfony\Component\String\UnicodeString;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class SecurityScheme implements JsonSerializable
{
    public function __construct(
        private string $type,
        private ?string $description = null,
        private ?string $name = null,
        private ?string $in = null,
        private ?string $scheme = null,
        private ?string $bearerFormat = null,
        private array $flows = [],
        private ?string $openIdConnectUrl = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $slugger = new UnicodeString($this->name ?: $this->type);
        $slugName = (string)$slugger->snake();

        return [
            $slugName => array_filter([
                'type' => $this->type,
                'description' => $this->description,
                'name' => $this->name,
                'in' => $this->in,
                'scheme' => $this->scheme,
                'bearerFormat' => $this->bearerFormat,
                'flows' => $this->flows,
                'openIdConnectUrl' => $this->openIdConnectUrl,
            ])
        ];
    }
}
