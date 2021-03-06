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