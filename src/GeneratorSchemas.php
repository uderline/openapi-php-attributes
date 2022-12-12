<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use ReflectionClass;

class GeneratorSchemas
{
    private array $components = [];

    public function append(ReflectionClass $reflectionClass)
    {
        $builder = new ComponentBuilder();

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
                    break;
            }
        }

        $this->components[] = $builder->getComponent();
    }

    public function build(): array
    {
        $array = [];

        foreach ($this->components as $component) {
            $array[$component->getName()] = $component;
        }

        return $array;
    }
}
