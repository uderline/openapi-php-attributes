<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Server implements JsonSerializable
{
    public function __construct(
        private string $url,
        private string $description = '',
    ) {
        //
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['url' => 'string', 'description' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'description' => $this->description,
        ];
    }
}
