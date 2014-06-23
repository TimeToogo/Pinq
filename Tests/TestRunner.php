<?php

namespace Pinq\Tests;

require_once 'TestBootstrapper.php';

class StreamingHTMLPrinter extends \PHPUnit_TextUI_ResultPrinter
{
    const PRINTER_CLASS = __CLASS__;
    
    public function __construct($out = NULL, $verbose = FALSE, $colors = FALSE, $debug = FALSE)
    {
        parent::__construct($out, $verbose, $colors, $debug);
        $this->autoFlush = true;
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
        echo '<span style="color:red">';
        parent::addError($test, $e, $time);
        echo '</span>';
    }
    
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        echo '<span style="color:red">';
        parent::addFailure($test, $e, $time);
        echo '</span>';
    }
    
    protected function printHeader()
    {
        echo '<span class="stats">';
        parent::printHeader();
        echo '</span>';
    }
    
    protected function printDefect(\PHPUnit_Framework_TestFailure $defect, $count)
    {
        echo '<span class="defect">';
        parent::printDefect($defect, $count);
        echo '</span>';
    }
    
    protected function printFooter(\PHPUnit_Framework_TestResult $result)
    {
        echo '<span class="result">';
        parent::printFooter($result);
        echo '</span>';
    }
}

$argv = [
    '--configuration', '../phpunit.xml.dist',
    '--printer', StreamingHTMLPrinter::PRINTER_CLASS,
];

if(isset($_GET['testsuite'])) {
    $argv[] = '--testsuite';
    $argv[] = $_GET['testsuite'];
}

$_SERVER['argv'] = $argv;


?>
<style>
#output {
    font-family: "Segoe UI", Arial, sans-serif;
    text-align: center;
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
</style>
<pre id='output'>
<?php

\PHPUnit_TextUI_Command::main();