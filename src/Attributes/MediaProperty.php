<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;

/**
 * This represents an open api property.
 * The property must have a type and a property name and can have a description and an example
 * If the property is an array, a PropertyItems must be set
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class MediaProperty implements PropertyInterface, JsonSerializable
{
    public function __construct(
        private string $contentMediaType,
        private string $contentEncoding,
    ) {
    }

    public function getContentMediaType(): string
    {
        return $this->contentMediaType;
    }

    #[ArrayShape([
        'type' => 'string',
        'format' => 'string'
    ])]
    public function jsonSerialize(): array
    {
        return [
            'type' => 'string',
            'format' => $this->contentEncoding
            // TODO For Body request ?
//            'contentMediaType' => $this->contentMediaType,
//            'contentEncoding' => $this->contentEncoding
        ];
    }

    #[Pure]
    public function getType(): string
    {
        return $this->getContentMediaType();
    }

    /**
     * @return string
     */
    public function getContentEncoding(): string
    {
        return $this->contentEncoding;
    }
}
