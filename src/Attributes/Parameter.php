<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use OpenApiGenerator\IllegalFieldException;

/**
 * Represents a parameter (e.g. /route/{id} where id is the parameter)
 *
 * A schema is automatically set to generate the parameter type
 */
#[Attribute]
class Parameter implements JsonSerializable
{
    protected ?string $name = null;
    protected array $schema;

    public function __construct(
        protected ?string $description = null,
        protected string $in = 'path',
        protected bool $required = false,
        protected mixed $example = '',
        protected mixed $format = ''
    ) {
        if ($in === 'path') {
            $this->required = true;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setParamType(string $paramType): self
    {
        $this->schema = match ($paramType) {
            'int' => ['type' => 'integer'],
            'bool' => ['type' => 'boolean'],
            'float', 'double' => ['type' => 'number', 'format' => $paramType],
            'mixed' => [],
            default => ['type' => $paramType],
        };

        return $this;
    }

    /**
     * @throws IllegalFieldException
     */
    public function jsonSerialize(): array
    {
        $this->schema ??= [];
        if (!$this->name) {
            throw IllegalFieldException::missingNameParameterValue();
        }

        $param = [
            'name' => $this->name,
            'in' => $this->in,
            'schema' => $this->formatSchema(),
        ];

        if ($this->required) {
            $param['required'] = true;
        }

        if ($this->description) {
            $param['description'] = $this->description;
        }

        return $param;
    }

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
