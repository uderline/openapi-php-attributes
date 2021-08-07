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
        private string $description,
        private string $url,
    ){
        //
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['description' => 'string', 'url' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
            'url' => $this->url,
        ];
    }
}
