<?php

require __DIR__ . '/Sami/vendor/autoload.php';

use Sami\Sami;
use Symfony\Component\Finder\Finder;
use Sami\Version\GitVersionCollection;

$Source = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir = __DIR__ . '/pinq/Source');

return new Sami($Source, array(
    'title'                => 'PINQ API',
    'build_dir'            => dirname(dirname(__DIR__)) . '/docs',
    'cache_dir'            => __DIR__ . '/cache',
    'default_opened_level' => 1,
));