<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use Countable;
use JsonSerializable;
use OpenApiGenerator\Types\SchemaType;

/**
 * A schema represents a list of properties
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Schema implements JsonSerializable, Countable
{
    /** @var Property[] */
    private array $properties = [];
    private ?string $schemaType = SchemaType::OBJECT;

    public function __construct(private ?array $required = null, private ?string $name = null)
    {
        //
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSchemaType(?string $type): self
    {
        $this->schemaType = $type;

        return $this;
    }

    public function jsonSerialize(): array
    {
        // By default, schemas are objects
        // The schema type becomes an array if the first and only property is an array
        if (count($this->properties) === 1) {
            $property = reset($this->properties);
            $this->schemaType = $property instanceof PropertyItems ? SchemaType::ARRAY : $this->schemaType;
        }

        $schema = [];

        if ($this->schemaType === SchemaType::ARRAY) {
            $schema += json_decode(json_encode(reset($this->properties)), true);
        } elseif ($this->schemaType === SchemaType::OBJECT) {
            $firstProperty = reset($this->properties);

            if ($firstProperty instanceof RefProperty || $firstProperty instanceof MediaProperty) {
                $schema = $firstProperty->jsonSerialize();
            } else {
                $array = [];

                foreach ($this->properties as $property) {
                    if ($property instanceof Property) {
                        $array['properties'][$property->getProperty()] = $property;
                    }
                }

                $schema += $array;
            }
        }

        // This is especially used for parameters which don't have media
        if (!$this->schemaType && count($this->properties)) {
            return reset($this->properties)->jsonSerialize();
        }

        $schema['type'] = $this->schemaType;

        if ($this->required) {
            $schema['required'] = $this->required;
        }

        return [
            $this->getMediaType() => [
                'schema' => $schema
            ]
        ];
    }

    private function getMediaType(): string
    {
        $hasMediaProp = array_filter(
            $this->properties,
            fn(?PropertyInterface $property): bool => $property instanceof MediaProperty
        );

        // Has a MediaProperty object, get the first - normally only one - property
        if (count($hasMediaProp) > 0) {
            $property = reset($this->properties);
            return $property->getContentMediaType();
        }

        if ($this->schemaType === SchemaType::STRING) {
            return 'text/plain';
        }

        // By default, return json type
        return 'application/json';
    }

    public function addProperty(PropertyInterface $property): void
    {
        $this->properties[] = $property;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function count(): int
    {
        return count($this->properties);
    }
}
