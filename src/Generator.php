<?php

declare(strict_types=1);

namespace OpenApiGenerator;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\Info;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Attributes\Security;
use OpenApiGenerator\Attributes\SecurityScheme;
use OpenApiGenerator\Attributes\Server;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class Generator
{
    public const OPENAPI_VERSION = "3.1.0";

    /**
     * API description
     *
     * @var array
     */
    private array $description = [];

    public function __construct(private readonly GeneratorHttp $generatorHttp)
    {
        //
    }

    /**
     * Create object with using package dependencies.
     *
     * @return Generator
     */
    public static function create(): Generator
    {
        return new self(new GeneratorHttp());
    }

    /**
     * Starting point of the Open Api generator
     *
     * Execution plan: get classes, find controllers containing paths, find schemas (open api components) and build them
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
            $this->loadSecurity($reflectionClass);
        }

        $this->description['paths'] = $this->generatorHttp->build();

        // Final array returned to be written in a file
        return $this->makeFinalArray();
    }

    /**
     * Info OA which is the head of the file.
     */
    private function loadInfo(ReflectionClass $reflectionClass): void
    {
        if ($infos = $reflectionClass->getAttributes(Info::class, ReflectionAttribute::IS_INSTANCEOF)) {
            $this->description["info"] = $infos[0]->newInstance();
        }
    }

    /**
     * A controller is a class containing paths itself containing methods.
     */
    private function loadController(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Controller::class))) {
            $this->generatorHttp->append($reflectionClass);
        }
    }

    /**
     * A schema is a component of the Open Api description.
     * It may be a model used in a request or a response.
     *
     * @throws IllegalFieldException
     */
    private function loadSchema(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Schema::class))) {
            $component = ComponentFactory::build($reflectionClass);
            $this->description['components']['schemas'][$component->getName()] = $component;
        }
    }

    private function loadServer(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Server::class))) {
            $serverAttributes = $reflectionClass->getAttributes(Server::class);

            foreach ($serverAttributes as $item) {
                $this->description['servers'][] = $item->newInstance();
            }
        }
    }

    private function loadSecurityScheme(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(SecurityScheme::class))) {
            $securitySchemes = $reflectionClass->getAttributes(SecurityScheme::class);

            foreach ($securitySchemes as $item) {
                $data = $item->newInstance()->jsonSerialize();
                $key = array_keys($data)[0];
                $this->description['components']['securitySchemes'][$key] = $data[$key];
            }
        }
    }

    private function loadSecurity(ReflectionClass $reflectionClass): void
    {
        if (count($reflectionClass->getAttributes(Security::class))) {
            $securityAttributes = $reflectionClass->getAttributes(Security::class);
            $security = reset($securityAttributes);

            $this->description['security'] = $security->newInstance()->jsonSerialize();
        }
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
}
