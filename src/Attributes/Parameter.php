<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;

/**
 * Represents a parameter (e.g. /route/{id} where id is the parameter)
 *
 * A schema is automatically set to generate the parameter type
 */
#[Attribute]
class Parameter implements JsonSerializable
{
    private string $name;
    private string $paramType;

    public function __construct(
        private ?string $description = null,
        private string $in = "path",
        private ?bool $required = null,
        private mixed $example = ""
    ) {
        if ($in === "path") {
            $this->required = true;
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setParamType(string $paramType): void
    {
        if ($paramType === 'int') {
            $this->paramType = 'integer';
            return;
        }
        $this->paramType = $paramType;
    }

    public function jsonSerialize(): array
    {
        $param = [
            "name" => $this->name,
            "in" => $this->in,
            "schema" => ["type" => $this->paramType]
        ];

        if ($this->required) {
            $param["required"] = $this->required;
        }

        if ($this->description) {
            $param["description"] = $this->description;
        }

        return $param;
    }
}
