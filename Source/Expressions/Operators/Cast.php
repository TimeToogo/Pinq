<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP cast operators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Cast
{
    const ARRAY_CAST = '(array) ';
    const BOOLEAN    = '(bool) ';
    const DOUBLE     = '(double) ';
    const INTEGER    = '(int) ';
    const OBJECT     = '(object) ';
    const STRING     = '(string) ';

    private static $castTypeMap = [
            self::ARRAY_CAST => 'array',
            self::BOOLEAN    => 'bool',
            self::DOUBLE     => 'double',
            self::INTEGER    => 'int',
            self::STRING     => 'string',
            self::OBJECT     => 'object'
    ];

    public static function doCast($castTypeOperator, $value)
    {
        settype($value, self::$castTypeMap[$castTypeOperator]);

        return $value;
    }
}
