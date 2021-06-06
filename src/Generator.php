<?php

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Schema;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class Generator
{
    private const OPENAPI_VERSION = "3.0.0";

    public function __construct()
    {
    }

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
        $info = null;
        $apiDefinition = [];

        $classes = get_declared_classes();
        foreach ($classes as $class) {
            try {
                $reflectionClass = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                continue;
            }

            // Info OA which is the head of the file
            if (count($reflectionClass->getAttributes(Info::class)) > 0) {
                $info = $reflectionClass->getAttributes(Info::class, ReflectionAttribute::IS_INSTANCEOF)[0];
                $apiDefinition["info"] = $info->newInstance();
            }

            // A controller with routes, call the HTTP Generator
            if (count($reflectionClass->getAttributes(Controller::class)) > 0) {
                $generate_http->append($reflectionClass);
                $apiDefinition = array_merge($apiDefinition, $generate_http->build());

                continue;
            }

            // A schema (often a model), call the Schema Generator
            if (count($reflectionClass->getAttributes(Schema::class)) > 0) {
                $generate_schemas->append($reflectionClass);
                $apiDefinition = array_merge($apiDefinition, $generate_schemas->build());
                continue;
            }
        }

        // Final array to transform to a json file
        return [
            "openapi" => self::OPENAPI_VERSION,
            "info" => $apiDefinition["info"],
            "paths" => $apiDefinition["paths"],
            "components" => $apiDefinition["components"]
        ];
    }
}
