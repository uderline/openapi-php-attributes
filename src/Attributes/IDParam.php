<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;

/**
 * Represents a parameter (e.g. /route/{id} where id is the parameter)
 *
 * A schema is automatically set to generate the parameter type
 */
#[Attribute]
class IDParam extends Parameter
{
    public function __construct(
        ?string $description = null,
        string $in = 'path',
        ?bool $required = null,
    ) {
        parent::__construct($description, $in, $required);
    }

    public function setParamType(string $paramType): void
    {
        // With frameworks' auto wiring, objects are injected as arguments.
        // If the object has a Property(Type::STRING, isObjectId: true), use the type of the property
        if (class_exists($paramType)) {
            $reflection = new \ReflectionClass($paramType);
            $attributes = $reflection->getAttributes();
            $paramType = array_reduce(
                $attributes,
                function (?string $previous, \ReflectionAttribute $attribute) {
                    if ($previous) {
                        return $previous;
                    }

                    $instance = $attribute->newInstance();
                    if ($instance instanceof Property && $instance->isObjectId()) {
                        // Use the example of the object
                        if ($instance->getExample()) {
                            $this->example = $instance->getExample();
                        }

                        return $instance->getType();
                    }

                    return null;
                }
            );
        }

        $this->schema = match ($paramType) {
            'int' => ['type' => 'integer', 'minimum' => 1],
            'bool' => ['type' => 'boolean'],
            'float', 'double' => ['type' => 'number', 'format' => $paramType],
            'mixed' => [],
            default => ['type' => $paramType],
        };
    }
}
