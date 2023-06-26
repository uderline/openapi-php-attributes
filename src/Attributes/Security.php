<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use stdClass;

#[Attribute(Attribute::TARGET_CLASS)]
class Security implements JsonSerializable
{
    /**
     * Be aware that security scheme keys are the slugified names or the type of the security scheme.
     * An empty array means that security is optional: https://spec.openapis.org/oas/v3.1.0#fixed-fields
     */
    public function __construct(private array $securitySchemeKeys = [])
    {
        // ...
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        if (empty($this->securitySchemeKeys)) {
            return [new stdClass()];
        }

        return array_map(fn(string $key) => [$key => []], $this->securitySchemeKeys);
    }
}
