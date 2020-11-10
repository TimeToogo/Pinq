<?php

namespace Pinq\Tests;

use PHPUnit\Runner\AfterTestHook;

class Timer implements AfterTestHook
{
    private static $log = [];

    public static function getLongRunningTests($amount)
    {
        arsort(Timer::$log);

        return array_slice(Timer::$log, 0, $amount);
    }

    public function executeAfterTest(string $test, float $time): void
    {
        self::$log[$test] = $time;
    }
}
