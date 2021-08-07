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
        private string $title,
        private string $version = '1.0.0',
        private string $summary = '',
        private string $description = '',
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'title' => 'string',
        'version' => 'string',
        'description' => 'string'
    ])]
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'version' => $this->version,
            'description' =>  $this->description,
        ];
    }
}
