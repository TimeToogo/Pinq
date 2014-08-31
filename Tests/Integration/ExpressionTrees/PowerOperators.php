<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class PowerOperators
{
    const TYPE = __CLASS__;

    public static function power($i, $e)
    {
        return $i ** $e;
    }

    public static function square()
    {
        $i = 5;
        return $i **= 2;
    }
}