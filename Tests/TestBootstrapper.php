<?php

namespace Pinq\Tests;

date_default_timezone_set('Australia/Melbourne');
error_reporting(-1);
set_time_limit(1000);
ini_set('display_errors', 'On');
$pinqAsProjectAutoLoaderPath = __DIR__ . '/../vendor/autoload.php';
$pinqAsDependencyAutoLoaderPath = __DIR__ . '/../../../../autoload.php';

if (file_exists($pinqAsProjectAutoLoaderPath)) {
    $composerAutoLoader = require $pinqAsProjectAutoLoaderPath;
} elseif (file_exists($pinqAsDependencyAutoLoaderPath)) {
    $composerAutoLoader = require $pinqAsDependencyAutoLoaderPath;
} else {
    throw new \Exception('Cannot load pinq tests: Pinq cannot be loaded, please load Pinq via composer');
}

$composerAutoLoader->addPsr4(__NAMESPACE__ . '\\', __DIR__);
