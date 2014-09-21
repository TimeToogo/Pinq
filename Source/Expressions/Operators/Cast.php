<?php

namespace Pinq\Expressions\Operators;

use Pinq\PinqException;

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

    /**
     * Performs the cast operation on the supplied value.
     *
     * @param string $castTypeOperator
     * @param mixed  $value
     *
     * @return mixed
     * @throws PinqException
     */
    public static function doCast($castTypeOperator, $value)
    {
        if (!isset(self::$castTypeMap[$castTypeOperator])) {
            throw new PinqException('Cast operator \'%s\' is not supported', $castTypeOperator);
        }

        settype($value, self::$castTypeMap[$castTypeOperator]);

        return $value;
    }
}
