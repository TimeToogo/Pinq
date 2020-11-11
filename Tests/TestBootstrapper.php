<?php

namespace Pinq\Tests;

date_default_timezone_set('Australia/Melbourne');
error_reporting(-1);
set_time_limit(2000);
ini_set('display_errors', 'On');
ini_set('memory_limit', '1G');

register_shutdown_function(function () {
    $error = error_get_last();
    if($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_PARSE], true)) {
        echo "-- Error occurred in {$error['file']} on line {$error['line']} --";
    }
});

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
