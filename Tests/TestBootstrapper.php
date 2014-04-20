<?php

namespace Pinq\Tests;

date_default_timezone_set('Australia/Melbourne');
error_reporting(-1);
set_time_limit(1000);
ini_set('display_errors', 'On');

$PinqAsProjectAutoLoaderPath = __DIR__ . '/../vendor/autoload.php';
$PinqAsDependencyAutoLoaderPath = __DIR__ . '/../../../../autoload.php';

if (file_exists($PinqAsProjectAutoLoaderPath)) {
    $ComposerAutoLoader = require $PinqAsProjectAutoLoaderPath;
} 
elseif (file_exists($PinqAsDependencyAutoLoaderPath)) {
    $ComposerAutoLoader = require $PinqAsDependencyAutoLoaderPath;
} 
else {
    throw new \Exception('Cannot load pinq tests: Penumbra cannot be loaded, please load Penumbra via composer');
}

$ComposerAutoLoader->addPsr4(__NAMESPACE__ . '\\', __DIR__);