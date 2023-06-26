<?php

namespace OpenApiGenerator;

use ReflectionClass;
use ReflectionMethod;

interface DynamicMethodResolverInterface
{
    public function setReflectionClass(ReflectionClass $reflectionClass): self;

    public function setReflectionMethod(ReflectionMethod $reflectionMethod): self;

    public function setMethod(Method $method): self;

    public function build(): array;
}