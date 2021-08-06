<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS)]
class Info implements JsonSerializable
{
    public function __construct(
        private string $title,
        private string $version,
        private string $description = "",
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $data = [
            "title" => $this->title,
            "version" => $this->version,
        ];

        if ($this->description) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
