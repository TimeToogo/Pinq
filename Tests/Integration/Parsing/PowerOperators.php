<?php

namespace Pinq\Tests\Integration\Parsing;

class PowerOperators
{
    const TYPE = __CLASS__;

    public static function binaryOperator()
    {
        4 ** 5;
    }

    public static function assignmentOperator()
    {
        $i **= 5;
    }
}