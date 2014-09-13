<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP assignment operators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Assignment
{
    const EQUAL           = '=';
    const EQUAL_REFERENCE = '=&';
    //Arithmetic
    const ADDITION       = '+=';
    const SUBTRACTION    = '-=';
    const MULTIPLICATION = '*=';
    const DIVISION       = '/=';
    const MODULUS        = '%=';
    const POWER          = '**=';
    //Bitwise
    const BITWISE_AND = '&=';
    const BITWISE_OR  = '|=';
    const BITWISE_XOR = '^=';
    const SHIFT_LEFT  = '<<=';
    const SHIFT_RIGHT = '>>=';
    //String
    const CONCATENATE = '.=';

    private static $toBinaryOperatorMap = [
            self::EQUAL           => null,
            self::EQUAL_REFERENCE => null,
            self::ADDITION        => Binary::ADDITION,
            self::SUBTRACTION     => Binary::SUBTRACTION,
            self::MULTIPLICATION  => Binary::MULTIPLICATION,
            self::DIVISION        => Binary::DIVISION,
            self::MODULUS         => Binary::MODULUS,
            self::POWER           => Binary::POWER,
            self::BITWISE_AND     => Binary::BITWISE_AND,
            self::BITWISE_OR      => Binary::BITWISE_OR,
            self::BITWISE_XOR     => Binary::BITWISE_XOR,
            self::SHIFT_LEFT      => Binary::SHIFT_LEFT,
            self::SHIFT_RIGHT     => Binary::SHIFT_RIGHT,
            self::CONCATENATE     => Binary::CONCATENATION,
    ];

    /**
     * Returns the equivalent binary operator of the supplied assignment operator
     * or null if there is no equivalent.
     *
     * @param string $assignment
     *
     * @return string|null
     */
    public static function toBinaryOperator($assignment)
    {
        return isset(self::$toBinaryOperatorMap[$assignment]) ? self::$toBinaryOperatorMap[$assignment] : null;
    }
}
