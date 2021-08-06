<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Server implements JsonSerializable
{
    public function __construct(
        private string $description,
        private string $url,
    ){
        //
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
            'url' => $this->url,
        ];
    }
}
