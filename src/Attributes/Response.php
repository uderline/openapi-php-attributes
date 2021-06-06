<?php

namespace OpenApiGenerator\Attributes;

use OpenApiGenerator\Types\ItemsType;
use OpenApiGenerator\Types\PropertyType;
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
                "description" => $this->description
            ]
        ];

        if ($this->ref) {
            $ref = explode('\\', $this->ref);
            $ref = last($ref);

            if (!$this->schemaType) {
                $array[$this->code]["content"]["application/json"]["schema"]['$ref'] = "#/components/schemas/$ref";
            }
            if ($this->schemaType === PropertyType::ARRAY) {
                $schema = new Schema(SchemaType::ARRAY);
                $schema->addProperty(new PropertyItems(ItemsType::REF, $this->ref));

                $array[$this->code]["content"]["application/json"]["schema"] = $schema;
            }
        } elseif ($this->schema) {
            $array[$this->code]["content"]["application/json"]["schema"] = $this->schema;
        }

        return $array;
    }

    public function setSchema(Schema $schema): void
    {
        $this->schema = $schema;
    }
}
