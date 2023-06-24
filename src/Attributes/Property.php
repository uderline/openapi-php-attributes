<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use BackedEnum;
use OpenApiGenerator\EnumInterface;
use OpenApiGenerator\IllegalFieldException;
use OpenApiGenerator\Types\PropertyType;
use JsonSerializable;

/**
 * This represents an open api property.
 * The property must have a type and a property name and can have a description and an example
 * If the property is an array, a PropertyItems must be set
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class Property implements PropertyInterface, ArrayProperty, JsonSerializable
{
    private ?PropertyItems $propertyItems = null;

    public function __construct(
        private string $type,
        private string $property,
        private string $description = '',
        private mixed $example = null,
        private ?string $format = null,
        // $enum can be an array (direct enum) or a string (class name of an enum: MyEnum::class)
        private null|array|string $enum = null,
        private ?string $ref = null,
        private bool $isObjectId = false,
        private array $extra = [],
        private bool $nullable = false,
    ) {
        if ($this->ref) {
            $ref = explode('\\', $this->ref);
            $this->ref = end($ref);
        }
    }

    public function setPropertyItems(PropertyItems $propertyItems): void
    {
        $this->propertyItems = $propertyItems;
        $this->propertyItems->setExample($this->example);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function isObjectId(): bool
    {
        return $this->isObjectId;
    }

    public function getExample(): mixed
    {
        return $this->example;
    }

    /**
     * @throws IllegalFieldException
     */
    public function jsonSerialize(): array
    {
        $type = $this->type;
        $minimum = null;

        if ($this->type === PropertyType::ARRAY) {
            if (!$this->propertyItems) {
                throw IllegalFieldException::missingArrayProperty();
            }

            return $this->propertyItems->jsonSerialize();
        }

        if ($this->type === PropertyType::REF) {
            $refType = ['$ref' => "#/components/schemas/$this->ref"];
            if ($this->nullable) {
                $refType = ['anyOf' => [['type' => 'null'], $refType]];
            }

            return $refType;
        }

        if ($this->type === PropertyType::ID) {
            $type = 'integer';
            $minimum = 1;
        }

        $array = [
            'description' => $this->description
        ];

        if ($this->nullable) {
            $array['anyOf'] = [['type' => 'null'], ['type' => $type]];
        } else {
            $array['type'] = $type;
        }

        if ($this->format) {
            $array['format'] = $this->format;
        }

        if ($this->enum) {
            $array['enum'] = $this->enum();
        }

        if ($this->example) {
            $array['example'] = $this->example;
        }

        if ($minimum) {
            $array['minimum'] = $minimum;
        }

        if ($this->extra) {
            $array = array_merge($array, $this->extra);
        }

        return $array;
    }

    private function enum(): ?array
    {
        if (is_array($this->enum)) {
            return $this->enum;
        }

        if(!enum_exists($this->enum)) {
            return null;
        }

        return array_map(
            static fn(BackedEnum $enum) => $enum->value,
            $this->enum::cases()
        );
    }

    public function isAnArray(): bool
    {
        return $this->propertyItems !== null;
    }
}
