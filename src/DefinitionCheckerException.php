<?php

namespace OpenApiGenerator;

use Exception;

class DefinitionCheckerException extends Exception
{
    public static function missingField(string $field): static
    {
        return new static("[Error] Missing field: $field");
    }

    public static function wrongFormat(string $field, string $expectingFormat): static
    {
        return new static("[Error] Wrong format for the field: $field. Expecting format: $expectingFormat");
    }
}