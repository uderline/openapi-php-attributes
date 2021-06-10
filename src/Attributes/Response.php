<?php

namespace OpenApiGenerator\Attributes;

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
#[\Attribute] class Response implements JsonSerializable
{
    private ?Schema $schema = null;

    public function __construct(
        private int $code = 200,
        private string $description = "",
        private ?string $responseType = null,
        private ?string $schemaType = null,
        private ?string $ref = null
    ) {
        if ($ref) {
            $this->schemaType = SchemaType::OBJECT;
            if ($this->ref) {
                $ref = explode('\\', $this->ref);
                $ref = end($ref);

                if ($this->schemaType === SchemaType::OBJECT) {
                    $schema = new Schema($this->schemaType);
                    $schema->addProperty(new RefProperty($ref));
                } elseif ($this->schemaType === SchemaType::ARRAY) {
                    $schema = new Schema(SchemaType::ARRAY);
                    $schema->addProperty(new PropertyItems(ItemsType::REF, $this->ref));
                }
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
        return [
            $this->code => [
                "description" => $this->description,
                "content" => $this->schema
            ]
        ];
    }

    public function setSchema(Schema $schema): void
    {
        $this->schema = $schema;
    }
}
