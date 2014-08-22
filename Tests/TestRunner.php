<?php

namespace Pinq\Tests;

require_once 'TestBootstrapper.php';

class StreamingHTMLPrinter extends \PHPUnit_TextUI_ResultPrinter
{
    const PRINTER_CLASS = __CLASS__;
    private static $shouldDebug;

    public function __construct($out = null, $verbose = false, $colors = false, $debug = false)
    {
        parent::__construct($out, $verbose, $colors, $debug);
        $this->colors = false;
        $this->printsHTML = true;
        $this->debug = self::$shouldDebug;
    }

    public static function setDebug($flag)
    {
        self::$shouldDebug = $flag;
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        parent::startTestSuite($suite);
        $this->maxColumn = 150;
    }

    public function write($buffer)
    {
        $originalBuffer = $buffer;
        $buffer = str_replace("\n", '<br>', $originalBuffer);

        //Force browser to load on new line
        if($buffer !== $originalBuffer
                && ($length = strlen($buffer)) < 2048) {
            $padding = str_repeat(' ', 2048 - $length);
            echo "<!--$padding-->";
        }

        echo $buffer;

        $this->incrementalFlush();
    }

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->write('<span style="color:red">');
        parent::addError($test, $e, $time);
        $this->write('</span>');
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->write('<span style="color:red">');
        parent::addFailure($test, $e, $time);
        $this->write('</span>');
    }

    protected function printHeader()
    {
        $this->write('<span class="stats">');
        parent::printHeader();
        $this->write('</span>');
    }

    protected function printDefect(\PHPUnit_Framework_TestFailure $defect, $count)
    {
        $this->write('<span class="defect">');
        parent::printDefect($defect, $count);
        $this->write('</span>');
    }

    protected function printFooter(\PHPUnit_Framework_TestResult $result)
    {
        $this->write('<span class="result">');
        parent::printFooter($result);
        $this->write('</span>');
        $this->write('<table class="timer">');
        $this->write('<tr><th class="title" colspan="2">Long running tests</th></tr>');
        $this->write('<tr><th>Test</th><th>Time</th></tr>');
        foreach (Timer::getLongRunningTests(10) as $testName => $timeTaken) {
            $timeTaken = round($timeTaken, 2) . 's';
            $this->write("<tr><td>$testName</td><td>{$timeTaken}</td></td>");
        }
        $this->write('</table>');
        $this->incrementalFlush();
    }
}

$argv = [];

$argv[] = '--configuration';
$argv[] = '../phpunit.xml.dist';
$argv[] = '--printer';
$argv[] = StreamingHTMLPrinter::PRINTER_CLASS;

if (isset($_GET['debug'])) {
   StreamingHTMLPrinter::setDebug(true);
}

if (isset($_GET['testsuite'])) {
    $argv[] = '--testsuite';
    $argv[] = $_GET['testsuite'];
}

$_SERVER['argv'] = $argv;

?>
<html>
    <head>
        <style>
        #output {
            font-family: "Segoe UI", Arial, sans-serif;
            text-align: left;
            margin-left: 50px;
            font-size: 15px;
            letter-spacing: 1px;
        }
        .defect {
            color: #c80000;
        }
        .stats {
            font-weight: bold;
        }
        .result {
            font-size: 20px;
        }
        .timer {
            width: 70%;
            margin-top: 30px;
        }
        .timer th:not(.title) {
            text-align: left;
        }
        .timer td {
            font-size: 12px;
        }
        </style>
    </head>
    <body>
        <pre id='output'>
        <?php

        \PHPUnit_TextUI_Command::main(false);

        ?>
        </pre>
    </body>
</html>
