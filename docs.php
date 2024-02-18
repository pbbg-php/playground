<?php

use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
                  ->files()
                  ->name('*.php')
                  ->in(__DIR__ . '/src');

return new Doctum\Doctum($iterator, [
    'title'     => 'Playground',
    'build_dir' => __DIR__ . '/docs',
]);
