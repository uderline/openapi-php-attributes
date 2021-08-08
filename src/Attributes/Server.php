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
     * @inheritDoc
     */
    #[ArrayShape(['url' => 'string', 'description' => 'string'])]
    public function jsonSerialize(): array
    {
        $data = [
            'url' => $this->url,
            'description' => $this->description,
        ];

        return removeEmptyValues($data);
    }
}
