<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use OpenApiGenerator\Types\ItemsType;

/**
 * Describe items of a property (an array)
 * If the array is an array of components, the ref argument can be set along with the type being an object
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class PropertyItems implements PropertyInterface, JsonSerializable
{
    public function __construct(
        private readonly string $type,
        private ?string $ref = null,
        private mixed $example = '',
        private readonly array $extra = [],
    ) {
        if ($this->ref) {
            $ref = explode('\\', $this->ref);
            $this->ref = end($ref);
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setExample(mixed $example): void
    {
        $this->example = $example;
    }

    public function jsonSerialize(): array
    {
        $array = [
            'type' => 'array'
        ];

        if ($this->type === ItemsType::REF && $this->getRef()) {
            $array['items'] = ['$ref' => "#/components/schemas/{$this->getRef()}"];
        } else {
            $array['items'] = [
                'type' => $this->type,
            ];

            if ($this->example) {
                $array['items']['example'] = $this->example;
            }
        }

        if ($this->extra) {
            $array = array_merge($array, $this->extra);
        }

        return $array;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }
}
