<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use JetBrains\PhpStorm\Pure;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Attributes\SecurityScheme;
use OpenApiGenerator\Attributes\Server;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class Generator
{
    public const OPENAPI_VERSION = "3.0.0";

    /**
     * API description
     *
     * @var array
     */
    private array $description = [];

    public function __construct(
        private GeneratorHttp $generatorHttp,
        private GeneratorSchemas $generatorSchemas,
    ) {
    }

    /**
     * Create object with using package dependencies.
     *
     * @return Generator
     */
    #[Pure]
    public static function create(): Generator
    {
        return new self(new GeneratorHttp(), new GeneratorSchemas());
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
                echo '[Warning] ReflectionException ' . $e->getMessage();

                continue;
            }

            $this->loadInfo($reflectionClass);
            $this->loadController($reflectionClass);
            $this->loadSchema($reflectionClass);
            $this->loadServer($reflectionClass);
            $this->loadSecurityScheme($reflectionClass);
        }

        $this->description['paths'] = $this->generatorHttp->build();
        $this->description['components']['schemas'] = $this->generatorSchemas->build();

        // Final array that will be transformed
        return $this->makeFinalArray();
    }

    /**
     * Array containing the entire API description
     */
    public function makeFinalArray(): array
    {
        $definition = [
            'openapi' => self::OPENAPI_VERSION,
            'info' => $this->description['info'],
            'servers' => $this->description['servers'] ?? [],
            'paths' => $this->description['paths'],
            'components' => $this->description['components'],
            'security' => $this->description['security'] ?? [],
        ];

        ApiDescriptionChecker::check($definition);

        return $definition;
    }

    /**
     * Info OA which is the head of the file.
     *
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadInfo(ReflectionClass $reflectionClass): void
    {
        if ($infos = $reflectionClass->getAttributes(Info::class, ReflectionAttribute::IS_INSTANCEOF)) {
            $this->description["info"] = $infos[0]->newInstance();
        }
    }


    /**
     * A controller with routes, call the HTTP Generator.
     *
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadController(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Controller::class))) {
            $this->generatorHttp->append($reflectionClass);
        }
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
                $this->description['servers'][] = $item->newInstance();
            }
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function loadSecurityScheme(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(SecurityScheme::class))) {
            $securitySchemas = $reflectionClass->getAttributes(SecurityScheme::class);

            foreach ($securitySchemas as $item) {
                $data = $item->newInstance()->jsonSerialize();
                $key = array_keys($data)[0];
                $this->description['components']['securitySchemes'][$key] = $data[$key];
            }

            $this->description['security'] = array_map(
                fn(string $canonicalName) => [$canonicalName => []],
                array_keys($this->description['components']['securitySchemes'])
            );
        }
    }
}
