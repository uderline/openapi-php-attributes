<?php

namespace OpenApiGenerator\Attributes;

interface ArrayProperty
{
    public function isAnArray(): bool;
}