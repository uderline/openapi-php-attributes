<?php

namespace OpenApiGenerator\Attributes;

use OpenApiGenerator\Types\MediaType;
use OpenApiGenerator\Types\PropertyType;
use JsonSerializable;
use OpenApiGenerator\Types\SchemaType;

/**
 * This represents an open api property.
 * The property must have a type and a property name and can have a description and an example
 * If the property is an array, a PropertyItems must be set
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_ALL)]
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

    public function jsonSerialize(): array
    {
        return [
            "type" => "string",
            "format" => $this->contentEncoding
            // TODO For Body request ?
//            "contentMediaType" => $this->contentMediaType,
//            "contentEncoding" => $this->contentEncoding
        ];
    }

    public function getType(): string
    {
        return $this->contentMediaType;
    }

    /**
     * @return string
     */
    public function getContentEncoding(): string
    {
        return $this->contentEncoding;
    }


}
