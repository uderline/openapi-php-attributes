<?php

declare(strict_types=1);

namespace OpenApiGenerator\Attributes;

use Attribute;
use JsonSerializable;
use OpenApiGenerator\Type;

/**
 * Root node for the paths part of the Open API definition
 *
 * On top of the normal route info, there are all parameters, the request body and the response.
 */
#[Attribute]
class Route implements JsonSerializable
{
    public const GET = 'get';
    public const POST = 'post';
    public const PUT = 'put';
    public const DELETE = 'delete';
    public const PATCH = 'patch';

    /** @var Parameter[] */
    private array $parameters = [];
    /** @var Response[] */
    private array $responses = [];
    private ?RequestBody $requestBody = null;
    private string $route;

    public function __construct(
        private readonly string $method,
        string $route,
        private readonly array $tags = [],
        private readonly string $summary = ''
    ) {
        // all routes must start with /.
        $this->route = !str_starts_with($route, '/') ? "/$route" : $route;
    }

    public function addResponse(Response $response): void
    {
        $this->responses[] = $response;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Parameters which are not explicitly declared will be automatically added
     *
     * @param Parameter[] $parameters
     */
    public function setParameters(array $parameters): void
    {
        // Just check if it's an array of parameters
        array_walk($parameters, fn(Parameter $param) => $this->parameters[] = $param);

        // Generate missing parameters that are not explicitly declared
        if (preg_match_all('#{([^}]+)}#', $this->route, $matches)) {
            $pathParams = $matches[1];
            $declaredParamsName = array_map(
                static fn(Parameter $parameter): string => $parameter->getName(), $this->parameters
            );
            foreach (array_diff($pathParams, $declaredParamsName) as $pathParam) {
                $param = new Parameter();
                $param->setName($pathParam);
                $param->setParamType(Type::STRING);
                $this->parameters[] = $param;
            }
        }
    }

    public function jsonSerialize(): array
    {
        // Auto
        $this->setParameters([]);

        $methodBody = [];

        if ($this->tags) {
            $methodBody['tags'] = $this->tags;
        }

        if ($this->summary) {
            $methodBody['summary'] = $this->summary;
        }

        if (count($this->parameters) > 0) {
            $methodBody['parameters'] = $this->parameters;
        }

        if ($this->requestBody && !$this->requestBody->isEmpty()) {
            $methodBody['requestBody'] = $this->requestBody;
        }

        if ($this->responses) {
            $responses = array_reduce($this->responses, function ($response, $current) {
                return $response + $current->jsonSerialize();
            }, []);

            $methodBody['responses'] = $responses;
        }

        return $methodBody;
    }

    public function setRequestBody(RequestBody $requestBody): void
    {
        $this->requestBody = $requestBody;
    }
}
