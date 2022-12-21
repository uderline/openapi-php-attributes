<?php

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Server;

class ApiDescriptionChecker
{
    /**
     * @throws DefinitionCheckerException
     */
    private function __construct(private array $definition)
    {
        $this->checkOpenApiVersion();
        $this->checkInfo();
        $this->checkServers();
        $this->checkSecurityScheme();
        $this->checkSecurity();
    }

    /**
     * Check that the open api version is superior or equals to 3.0.0
     * (@link https://spec.openapis.org/oas/v3.1.0#openapi-object)
     *
     * @throws DefinitionCheckerException
     */
    private function checkOpenApiVersion(): void
    {
        if (!isset($this->definition['openapi'])) {
            throw DefinitionCheckerException::missingField('openapi');
        }

        if (!preg_match('/^[3-9]{1}\.[0-9]{1}\.[0-9]{1}$/', $this->definition["openapi"])) {
            throw DefinitionCheckerException::wrongFormat('openapi', '[>=3].x.x');
        }
    }

    /**
     * The info field and the title field of the info are mandatory
     * (@link https://spec.openapis.org/oas/v3.1.0#info-object)
     *
     * @throws DefinitionCheckerException
     */
    private function checkInfo(): void
    {
        if (!isset($this->definition['info'])) {
            throw DefinitionCheckerException::missingField('info');
        }
    }

    /**
     * If the servers field present, the url field is mandatory
     * (@link https://spec.openapis.org/oas/v3.1.0#server-object)
     *
     * @throws DefinitionCheckerException
     */
    private function checkServers(): void
    {
        if (!isset($this->definition['servers'])) {
            return;
        }

        foreach ($this->definition['servers'] as $server) {
            if ($server instanceof Server && !$server->getUrl()) {
                throw DefinitionCheckerException::missingField('servers.url');
            }
        }
    }

    private function checkSecurityScheme(): void
    {
        if (!isset($this->definition['securitySchemes'])) {
            return;
        }

        foreach ($this->definition['securitySchemes'] as $securityScheme) {
            switch ($securityScheme["type"]) {
                case "apiKey":
                    if (empty($securityScheme["name"]) || !in_array(
                            $securityScheme["in"],
                            ['query', 'header', 'cookie'],
                            true
                        )) {
                        throw new \InvalidArgumentException("SecurityScheme: apiKey must have name and in");
                    }
                    break;
                case "http":
                    if (empty($securityScheme["scheme"])) {
                        throw new \InvalidArgumentException("SecurityScheme: http must have scheme");
                    }
                    break;
                case "mutualTLS":
                    break;
                case "oauth2":
                    if (empty($securityScheme["flows"])) {
                        throw new \InvalidArgumentException("SecurityScheme: oauth2 must have flows");
                    }
                    break;
                case "openIdConnect":
                    if (empty($securityScheme["openIdConnectUrl"])) {
                        throw new \InvalidArgumentException("SecurityScheme: openIdConnect must have openIdConnectUrl");
                    }
                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Invalid security scheme type: should be one of "apiKey", "http", "oauth2", "openIdConnect"'
                    );
            }
        }
    }

    private function checkSecurity(): void
    {
        if (!isset($this->definition['security'])) {
            return;
        }

        foreach ($this->definition['security'] as $security) {
            if ($security instanceof \stdClass) {
                continue;
            }

            $availableValues = array_keys($this->definition['components']['securitySchemes']);
            $securityName = array_keys($security)[0];

            if (!in_array($securityName, $availableValues, true)) {
                throw new \InvalidArgumentException(
                    "Security: security scheme not found. Please choose one of the followings: " .
                    implode(', ', $availableValues)
                );
            }
        }
    }

    /**
     * Check the API description for any omitted mandatory fields or wrong formats.
     */
    public static function check(array $description): bool
    {
        try {
            new self($description);
        } catch (DefinitionCheckerException $exception) {
            echo $exception;

            return false;
        }

        return true;
    }
}