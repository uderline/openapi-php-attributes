<?php

use Symfony\Component\Finder\Finder;

include __DIR__ . '/../../vendor/autoload.php';

$files = Finder::create()->files()->name('*.php')->in($argv[1]);

foreach ($files as $file) {
    include_once $file->getPathName();
}

$generator = (new \OpenApiGenerator\Generator())->generate();

$schema = stripslashes(json_encode($generator, JSON_PRETTY_PRINT));

file_put_contents($argv[2] . '/oa3.json', $schema);

