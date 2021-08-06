<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use JetBrains\PhpStorm\Pure;
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
    public const OPENAPI_VERSION = "3.0.0";

    /**
     * default definition json schema.
     *
     * @var array
     */
    private array $definition = [
        'info' => [],
        'paths' => [],
        'servers' => [],
        'components' => [
            'schemas' => [],
            'securitySchemes' => [],
        ],
    ];

    public function __construct(
        private GeneratorHttp $generatorHttp,
        private GeneratorSchemas $generatorSchemas,
    ) {
    }

    /**
     * @return Generator
     */
    #[Pure]
    public static function factory(): Generator
    {
        return new self(new GeneratorHttp, new GeneratorSchemas);
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
        $classes = get_declared_classes();

        foreach ($classes as $class) {
            try {
                $reflectionClass = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                echo '[Waring] ReflectionException ' . $e->getMessage();

                continue;
            }

            $this->loadInfo($reflectionClass);

            if ($this->loadController($reflectionClass)) {
                continue;
            }

            $this->loadSchema($reflectionClass);

            $this->loadServer($reflectionClass);
            $this->loadSchemaSecurity($reflectionClass);
        }

        // Final array to transform to a json file
        return [
            'openapi' => self::OPENAPI_VERSION,
            'info' => $this->definition['info'],
            'servers' => $this->definition['servers'],
            'paths' => $this->definition['paths'],
            'components' => $this->definition['components'],
        ];
    }

    /**
     * Info OA which is the head of the file.
     *
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadInfo(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Info::class))) {
            $info = $reflectionClass->getAttributes(Info::class, ReflectionAttribute::IS_INSTANCEOF)[0];
            $this->definition["info"] = $info->newInstance();
        }
    }


    /**
     * A controller with routes, call the HTTP Generator.
     *
     * @param ReflectionClass $reflectionClass
     * @return bool
     */
    private function loadController(ReflectionClass $reflectionClass): bool
    {
        if (count($reflectionClass->getAttributes(Controller::class))) {
            $this->generatorHttp->append($reflectionClass);
            $this->definition = array_merge($this->definition, $this->generatorHttp->build());

            return true;
        }

        return false;
    }

    /**
     * A schema (often a model), call the Schema Generator.
     *
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadSchema(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Schema::class))) {
            $this->generatorSchemas->append($reflectionClass);
            $this->definition['components']['schemas'] = $this->generatorSchemas->build();
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadServer(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Server::class))) {
            $serverAttributes = $reflectionClass->getAttributes(Server::class);

            foreach ($serverAttributes as $item) {
                $this->definition['servers'][] = $item->newInstance();
            }
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadSchemaSecurity(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(SchemaSecurity::class))) {
            $securitySchemas = $reflectionClass->getAttributes(SchemaSecurity::class);

            foreach ($securitySchemas as $item) {
                $data = $item->newInstance()->jsonSerialize();
                $key = array_keys($data)[0];
                $this->definition['components']['securitySchemes'][$key] = $data[$key];
            }
        }
    }
}
