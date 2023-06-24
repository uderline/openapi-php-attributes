<?php

namespace OpenApiGenerator;

use Exception;

class IllegalFieldException extends Exception
{
    public static function missingArrayProperty(): static
    {
        return new static("[Error] Missing array type property");
    }

    public static function missingPropertyItemsOnArrayType(): static
    {
        return new static("[Error] Missing property items on an array type property");
    }

    public static function missingNameParameterValue(): static
    {
        return new static("[Error] Missing the name parameter's value");
    }
}