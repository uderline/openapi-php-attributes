<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS)]
class Info implements JsonSerializable
{
    public function __construct(
        private readonly string $title,
        private readonly string $version = '1.0.0',
        private readonly string $description = '',
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'version' => $this->version,
            'description' =>  $this->description,
        ];
    }
}
