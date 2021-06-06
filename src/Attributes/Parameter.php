<?php

namespace OpenApiGenerator\Attributes;

use JsonSerializable;

/**
 * Represents a parameter (e.g. /route/{id} where id is the parameter)
 *
 * A schema is automatically set to generate the parameter type
 */
#[\Attribute]
class Parameter implements JsonSerializable
{
    private string $name;
    private ?Schema $schema = null;
    private string $paramType;

    public function __construct(
        private string $description,
        private bool $required = true,
        private string $in = "path",
        private mixed $example = ""
    ) {
    }

    public function setSchema(Schema $schema): void
    {
        $this->schema = $schema;
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
        if (!$this->schema) {
            $this->schema = new Schema(schemaType: $this->paramType, name: $this->name);
        }

        $array = [
            "name" => $this->name,
            "in" => $this->in,
            "description" => $this->description,
            "required" => $this->required,
        ];

        $array["schema"] = $this->schema;

        return $array;
    }
}
