<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use InvalidArgumentException;
use JsonSerializable;

/**
 * This represents an OpenAPI path which has several method routes
 * Paths are merged in the generator
 */
class Path implements JsonSerializable
{
    private string $path;
    /** @var Method[] */
    private array $methods = [];

    public function __construct(Method $method)
    {
        $this->path = $method->getPath();
        $this->addMethod($method);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function addMethod(Method $method): self
    {
        if (!$this->hasSamePath($method)) {
            throw new InvalidArgumentException('Method path does not match the path');
        }

        $this->methods[] = $method;

        return $this;
    }

    public function hasSamePath(Method $method): bool
    {
        return $this->path === $method->getPath();
    }

    public function jsonSerialize(): array
    {
        $methods = [];
        foreach ($this->methods as $method) {
            $methods[$method->getMethod()] = $method->jsonSerialize();
        }

        return $methods;
    }
}
