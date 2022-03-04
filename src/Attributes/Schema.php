<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use OpenApiGenerator\Types\SchemaType;
use JsonSerializable;

/**
 * A schema represents a list of properties
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Schema implements JsonSerializable
{
    private array $properties = [];
    private bool $noMedia = false;

    public function __construct(
        private string $schemaType = SchemaType::OBJECT,
        private ?array $required = null,
        private ?string $name = null
    ) {
        //
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    private function getMediaType(): string
    {
        $hasMediaProp = array_filter(
            $this->properties,
            fn(?PropertyInterface $property): bool => $property instanceof MediaProperty
        );

        // Has a MediaProperty object, get the first - and normally only on - property
        if (count($hasMediaProp) > 0) {
            $property = reset($this->properties);
            return $property->getContentMediaType();
        }

        // By default, return json type
        return 'application/json';
    }

    public function jsonSerialize(): array
    {
       $schema = [
           'type' => $this->schemaType
       ];

        if ($this->schemaType === SchemaType::ARRAY) {
            $schema += json_decode(json_encode(reset($this->properties)), true);
        } elseif ($this->schemaType === SchemaType::OBJECT) {
            $firstProperty = reset($this->properties);

            if ($firstProperty instanceof RefProperty || $firstProperty instanceof MediaProperty) {
                $schema = $firstProperty->jsonSerialize();
            } else {
                $array = [];

                if ($this->required) {
                    $array['required'] = $this->required;
                }

                foreach ($this->properties as $property) {
                    if ($property instanceof Property) {
                        $array['properties'][$property->getProperty()] = $property;
                    }
                }

                $schema += $array;
            }
        }

        // This is especially used for parameters which don't have media
        if ($this->noMedia) {
            return $schema;
        }

        return [
            $this->getMediaType() => [
                'schema' => $schema
            ]
        ];
    }

    public function addProperty(PropertyInterface $property): void
    {
        $this->properties[] = $property;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setNoMedia(bool $noMedia): void
    {
        $this->noMedia = $noMedia;
    }
}
