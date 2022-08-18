<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use OpenApiGenerator\Types\ItemsType;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\ResponseType;
use OpenApiGenerator\Types\SchemaType;
use JsonSerializable;

/**
 * A response is composed of a code, a description and a response type
 * Additionally, a schema type can be added (array or object) and a ref which will return any other property
 * Consider the ref parameter like a shortcut
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class Response implements JsonSerializable
{
    private ?Schema $schema = null;

    public function __construct(
        private int $code = 200,
        private string $description = '',
        private ?string $responseType = null,
        private ?string $schemaType = SchemaType::OBJECT,
        private ?string $ref = null,
        private array $extra = [],
    ) {
        if ($ref) {
            $this->schema = new Schema($schemaType);

            if ($schemaType === SchemaType::OBJECT) {
                $this->schema->addProperty(new RefProperty($ref));
            } elseif ($schemaType === SchemaType::ARRAY) {
                $this->schema->addProperty(new PropertyItems(ItemsType::REF, $ref));
            }
        }
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
                'description' => $this->description
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
}
