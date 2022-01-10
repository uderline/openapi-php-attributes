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

    private array $getParams = [];
    private ?Response $response = null;
    private ?RequestBody $requestBody = null;

    public function __construct(
        private string $method,
        private string $route,
        private array $tags = [],
        private string $summary = ''
    ) {
        //
    }

    public function addParam(Parameter $params): void
    {
        $this->getParams[] = $params;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        // all routes must starting with /.
        if (substr($this->route, 0, 1) !== '/') {
            return '/' . $this->route;
        }

        return $this->route;
    }

    public function getGetParams(): array
    {
        return $this->getParams;
    }

    /**
     * @param Parameter[] $getParams
     * @return void
     */
    public function setGetParams(array $getParams): void
    {
        // Just check if it's an array of GetParam and add it
        array_walk($getParams, fn(Parameter $param) => $this->getParams[] = $param);

        if (preg_match_all('#{([^}]+)}#', $this->route, $matches)) {
            $pathParams = $matches[1];
            $declaredParamsName = array_map(static fn(Parameter $parameter): string => $parameter->getName(), $getParams);
            foreach (array_diff($pathParams, $declaredParamsName) as $pathParam) {
                $param = new Parameter();
                $param->setName($pathParam);
                $param->setParamType(Type::STRING);
                $this->getParams[] = $param;
            }
        }
    }

    public function jsonSerialize(): array
    {
        $array = [];

        if ($this->tags) {
            $array[$this->getRoute()][$this->method]['tags'] = $this->tags;
        }

        if ($this->summary) {
            $array[$this->getRoute()][$this->method]['summary'] = $this->summary;
        }

        if (count($this->getParams) > 0) {
            $array[$this->getRoute()][$this->method]['parameters'] = $this->getParams;
        }

        if ($this->requestBody && !$this->requestBody->empty()) {
            $array[$this->getRoute()][$this->method]['requestBody'] = $this->requestBody;
        }

        if ($this->response) {
            $array[$this->getRoute()][$this->method]['responses'] = $this->response;
        }

        return $array;
    }

    public function setRequestBody(RequestBody $requestBody)
    {
        $this->requestBody = $requestBody;
    }
}
