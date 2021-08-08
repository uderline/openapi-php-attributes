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
        private string $termsOfService = '',
        private ?array $contact = null,
        private ?array $license = null,
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'title' => 'string',
        'version' => 'string',
        'summary' => 'string',
        'description' => 'string'
    ])]
    public function jsonSerialize(): array
    {
        $data = [
            'title' => $this->title,
            'version' => $this->version,
            'summary' => $this->summary,
            'description' =>  $this->description,
            'termsOfService' => $this->termsOfService,
            'contact' => $this->contact,
            'license' => $this->license,
        ];

        return removeEmptyValues($data);
    }
}
