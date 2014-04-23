<?php 

namespace Pinq\Tests;

require_once 'TestBootstrapper.php';
$argv = ['--configuration', '../phpunit.xml.dist'];
$_SERVER['argv'] = $argv;
\PHPUnit_TextUI_Command::main();