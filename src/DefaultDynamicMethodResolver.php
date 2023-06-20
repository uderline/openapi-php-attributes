<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Schema;

final class DefaultDynamicMethodResolver implements DynamicMethodResolverInterface
{
    private \ReflectionClass $reflectionClass;
    private \ReflectionMethod $reflectionMethod;
    private Method $method;

    public function setReflectionClass(\ReflectionClass $reflectionClass): self
    {
        $this->reflectionClass = $reflectionClass;

        return $this;
    }

    public function setReflectionMethod(\ReflectionMethod $reflectionMethod): self
    {
        $this->reflectionMethod = $reflectionMethod;

        return $this;
    }

    public function setMethod(Method $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * A method contains:
     * - a schema that describes the request body
     * - a request body that contains the schema
     * - a route that contains (but not only) the path, method, tags, summary, parameters and the request body
     */
    public function build(): array
    {
        $route = clone $this->method->getRoute();
        $route->setGetParams($this->method->getParameters());
        $route->setRequestBody($this->method->getRequestBody());
        $route->addResponse($this->method->getResponse());

        return $route->jsonSerialize();
    }
}
