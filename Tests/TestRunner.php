<?php

namespace Pinq\Tests;

require_once 'TestBootstrapper.php';

$argv = [
    '--configuration', 'Configuration.xml',
];
$_SERVER['argv'] = $argv;

\PHPUnit_TextUI_Command::main();
