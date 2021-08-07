<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;
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
    private array $schema;

    public function __construct(
        private ?string $description = null,
        private string $in = 'path',
        private ?bool $required = null,
        private mixed $example = '',
        private mixed $format = ''
    ) {
        if ($in === 'path') {
            $this->required = true;
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setParamType(string $paramType): void
    {
        $this->schema = match ($paramType) {
            'int' => ['type' => 'integer'],
            'bool' => ['type' => 'boolean'],
            'float', 'double' => ['type' => 'number', 'format' => $paramType],
            'mixed' => [],
            default => ['type' => $paramType],
        };
    }

    #[ArrayShape([
        'name' => 'string',
        'in' => 'string',
        'schema' => 'array',
        'description' => 'null|string',
        'required' => 'bool|null'
    ])]
    public function jsonSerialize(): array
    {
        $param = [
            'name' => $this->name,
            'in' => $this->in,
            'schema' => $this->formatSchema(),
        ];

        if ($this->required) {
            $param['required'] = $this->required;
        }

        if ($this->description) {
            $param['description'] = $this->description;
        }

        return $param;
    }

    /**
     * Format schema for serialize to json.
     *
     * @return array
     */
    private function formatSchema(): array
    {
        $schema = $this->schema;

        if ($format = $this->format ?? $schema['format']) {
            $schema['format'] = $format;
        }

        if ($this->example) {
            $schema['example'] = $this->example;
        }

        return $schema;
    }
}
