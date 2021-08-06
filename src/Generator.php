<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Attributes\SchemaSecurity;
use OpenApiGenerator\Attributes\Server;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class Generator
{
    private const OPENAPI_VERSION = "3.0.0";

    /**
     * Start point of the Open Api generator
     *
     * Execution plan: get classes from directory, find controllers, schemas, get Attributes,
     * add each attribute to some sort of tree then transform it to a json file
     *
     * Et voilà !
     */
    public function generate(): array
    {
        $generate_http = new GenerateHttp();
        $generate_schemas = new GenerateSchemas();
        $classes = get_declared_classes();
        $apiDefinition = [
            'info' => [],
            'paths' => [],
            'servers' => [],
            'components' => [
                'schemas' => [],
                'securitySchemes' => [],
            ],
        ];

        foreach ($classes as $class) {
            try {
                $reflectionClass = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                echo '[Waring] ReflectionException ' . $e->getMessage();

                continue;
            }

            // Info OA which is the head of the file
            if (count($reflectionClass->getAttributes(Info::class))) {
                $info = $reflectionClass->getAttributes(Info::class, ReflectionAttribute::IS_INSTANCEOF)[0];
                $apiDefinition["info"] = $info->newInstance();
            }

            // A controller with routes, call the HTTP Generator
            if (count($reflectionClass->getAttributes(Controller::class))) {
                $generate_http->append($reflectionClass);
                $apiDefinition = array_merge($apiDefinition, $generate_http->build());

                continue;
            }

            // A schema (often a model), call the Schema Generator
            if (count($reflectionClass->getAttributes(Schema::class))) {
                $generate_schemas->append($reflectionClass);
                $apiDefinition['components']['schemas'] = $generate_schemas->build();

                continue;
            }

            // A simple server.
            if (count($reflectionClass->getAttributes(Server::class))) {
                $serverAttributes = $reflectionClass->getAttributes(Server::class);

                foreach ($serverAttributes as $item) {
                    $apiDefinition['servers'][] = $item->newInstance()->jsonSerialize();
                }
            }

            if (count($reflectionClass->getAttributes(SchemaSecurity::class))) {
                $securitySchemas = $reflectionClass->getAttributes(SchemaSecurity::class);

                foreach ($securitySchemas as $item) {
                    $data = $item->newInstance()->jsonSerialize();
                    $key = array_keys($data)[0];
                    $apiDefinition['components']['securitySchemes'][$key] = $data[$key];
                }
            }
        }

        // Final array to transform to a json file
        return [
            'openapi' => self::OPENAPI_VERSION,
            'info' => $apiDefinition['info'],
            'servers' => $apiDefinition['servers'],
            'paths' => $apiDefinition['paths'],
            'components' => $apiDefinition['components'],
        ];
    }
}
