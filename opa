#!/usr/bin/env php
<?php declare(strict_types=1);

$autoload_path = null;

foreach ([__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php'] as $autoload) {
    if (file_exists($autoload)) {
        $autoload_path = $autoload;
        break;
    }
}

if (!$autoload_path) {
    die;
}

require $autoload_path;

use Symfony\Component\Finder\Finder;

$files = Finder::create()->files()->name('*.php')->in($argv[1]);

foreach ($files as $autoload) {
    include_once $autoload->getPathName();
}

$generator = \OpenApiGenerator\Generator::factory()->generate();

$schema = stripslashes(json_encode($generator, JSON_PRETTY_PRINT));

file_put_contents($argv[2] . '/oa3.json', $schema);
