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

            match ($name) {
                Schema::class => $builder->addSchema($instance, $className),
                Property::class => $builder->addProperty($instance),
                PropertyItems::class => $builder->addPropertyItems($instance),
            };
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
