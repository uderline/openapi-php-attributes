<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Examples\Dummy;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;

enum DummyBackedEnum: string
{
    case VALUE1 = 'value1';
    case VALUE2 = 'value2';
}
