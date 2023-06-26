<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use ReflectionClass;

abstract class ComponentFactory
{
    private function __construct()
    {
        //
    }

    /**
     * @throws IllegalFieldException
     */
    public static function build(ReflectionClass $reflectionClass): Schema
    {
        $builder = new SchemaBuilder(true);

        foreach ($reflectionClass->getAttributes() as $attribute) {
            $name = $attribute->getName();
            $instance = $attribute->newInstance();
            $className = $reflectionClass->getName();

            switch ($name) {
                case Schema::class:
                    $builder->addSchema($instance, $className);
                    break;
                case Property::class:
                    $builder->addProperty($instance);
                    break;
                case PropertyItems::class:
                    $builder->addPropertyItems($instance);
                    break;
                default:
                    // Ignore other attributes
                    break;
            }
        }

        return $builder->getComponent();
    }
}
