<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use OpenApiGenerator\Types\RequestBodyType;

/**
 * The entire request body where you can specify the return type and a schema
 */
#[Attribute]
class RequestBody implements JsonSerializable
{
    private ?Schema $schema = null;

    public function __construct(
        private ?string $type = null
    ) {
        $this->type ??= RequestBodyType::JSON;
    }

    public function setSchema(Schema $schema): void
    {
        $this->schema = $schema;
    }

    public function addProperty(PropertyInterface $property): void
    {
        if (!$this->schema) {
            $this->schema = new Schema();
        }

        $this->schema->addProperty($property);
    }

    public function isEmpty(): bool
    {
        return !$this->schema;
    }

    public function jsonSerialize(): array
    {
        if (!$this->schema) {
            return [];
        }

        // TODO: deal with media content or any other Types (cf. $this->type)
        return $this->schema->jsonSerialize();
    }
}
