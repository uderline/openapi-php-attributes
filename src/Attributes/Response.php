<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use Countable;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\ItemsType;
use OpenApiGenerator\Types\SchemaType;
use JsonSerializable;

/**
 * A response is composed of a code, a description and a response type
 * Additionally, a schema type can be added (array or object) and a ref which will return any other property
 * Consider the ref parameter like a shortcut
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class Response implements JsonSerializable, Countable
{
    private ?Schema $schema = null;

    public function __construct(
        private readonly int $code = 200,
        private readonly string $description = '',
        private ?string $responseType = null,
        private readonly ?string $schemaType = SchemaType::OBJECT,
        private readonly ?string $ref = null,
        private readonly array $extra = [],
    ) {
        if ($this->ref) {
            $this->schema = new Schema();

            if ($this->schemaType === SchemaType::OBJECT) {
                $this->schema->addProperty(new RefProperty($this->ref));
            } elseif ($this->schemaType === SchemaType::ARRAY) {
                $property = new Property(Type::ARRAY, "");
                $property->setPropertyItems(new PropertyItems(ItemsType::REF, $this->ref));

                $this->schema->addProperty($property);
            }
        }
    }

    public function addProperty(PropertyInterface $property): void
    {
        if (!$this->schema) {
            $this->schema = new Schema();
        }

        $this->schema->addProperty($property);
    }

    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    public function setResponseType(?string $responseType): void
    {
        $this->responseType = $responseType;
    }

    public function jsonSerialize(): array
    {
        $array = [
            $this->code => [
                'description' => $this->description,
            ]
        ];

        if ($this->schema) {
            $array[$this->code]['content'] = $this->schema;
        }

        if ($this->extra) {
            $array[$this->code] = array_merge($array[$this->code], $this->extra);
        }

        return $array;
    }

    public function setSchema(Schema $schema): void
    {
        $this->schema = $schema;
    }

    public function count(): int
    {
        return count($this->schema);
    }
}
