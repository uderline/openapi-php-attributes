<?php

declare(strict_types=1);

namespace OpenApiGenerator\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class DefinitionCheckerException extends Exception
{
    #[Pure]
    public static function missingField(string $field): static
    {
        return new static("[Error] Missing field: $field");
    }

    #[Pure]
    public static function wrongFormat(string $field, string $expectingFormat): static
    {
        return new static("[Error] Wrong format for the field: $field. Expecting format: $expectingFormat");
    }
}
