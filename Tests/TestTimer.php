<?php

namespace Pinq\Tests;

use Exception;
use PHPUnit_Framework_Test;

class Timer implements \PHPUnit_Framework_TestListener
{
    private static $log = [];

    public static function getLongRunningTests($amount)
    {
        arsort(Timer::$log);

        return array_slice(Timer::$log, 0, $amount);
    }

    public function endTest(\PHPUnit_Framework_Test $test, $length)
    {
        self::$log[get_class($test) . '::' . $test->getName()] = $length;
    }

    public function startTest(\PHPUnit_Framework_Test $test) {}
    public function addWarning(PHPUnit_Framework_Test $test, \PHPUnit_Framework_Warning $e, $time) {}
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time) {}
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite) { }
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite) {}
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}
}
