<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use Attribute;
use JsonSerializable;
use OpenApiGenerator\Attributes\PropertyInterface;
use OpenApiGenerator\Types\PropertyType;

class RefProperty implements PropertyInterface, JsonSerializable
{
    private string $route = "#/components/schemas/";

    public function __construct(
        private string $ref,
    ) {
        $ref = explode('\\', $this->ref);
        $this->ref = end($ref);
    }

    public function setComponentRoutePrefix(string $route): void
    {
        $this->route = $route;
    }

    public function jsonSerialize(): array
    {
        return ['$ref' => "{$this->route}{$this->ref}"];
    }

    public function getType(): string
    {
        return PropertyType::REF;
    }
}
