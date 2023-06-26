<?php

namespace OpenApiGenerator;

use Exception;

class IllegalFieldException extends Exception
{
    public static function missingArrayProperty(): static
    {
        return new static("[Error] Missing array type property");
    }

    public static function missingNameParameterValue(): static
    {
        return new static("[Error] Missing the name parameter's value");
    }

    public static function missingSchema(): static
    {
        return new static("[Error] Missing schema (did you forget to add the Schema attribute on your class ?)");
    }
}