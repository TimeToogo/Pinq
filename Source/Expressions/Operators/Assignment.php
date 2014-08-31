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
    //Bitwise
    const BITWISE_AND = '&=';
    const BITWISE_OR  = '|=';
    const BITWISE_XOR = '^=';
    const SHIFT_LEFT  = '<<=';
    const SHIFT_RIGHT = '>>=';
    //String
    const CONCATENATE = '.=';

    private static $assignments;

    public static function doAssignment(&$left, $operator, $right)
    {
        if (self::$assignments === null) {
            self::$assignments = [
                    self::EQUAL          => function (&$l, $r) { return $l = $r; },
                    self::CONCATENATE    => function (&$l, $r) { return $l .= $r; },
                    self::BITWISE_AND    => function (&$l, $r) { return $l &= $r; },
                    self::BITWISE_OR     => function (&$l, $r) { return $l |= $r; },
                    self::BITWISE_XOR    => function (&$l, $r) { return $l ^= $r; },
                    self::SHIFT_LEFT     => function (&$l, $r) { return $l <<= $r; },
                    self::SHIFT_RIGHT    => function (&$l, $r) { return $l >>= $r; },
                    self::ADDITION       => function (&$l, $r) { return $l += $r; },
                    self::SUBTRACTION    => function (&$l, $r) { return $l -= $r; },
                    self::MULTIPLICATION => function (&$l, $r) { return $l *= $r; },
                    self::DIVISION       => function (&$l, $r) { return $l /= $r; },
                    self::MODULUS        => function (&$l, $r) { return $l %= $r; }
            ];
        }

        $assignment = self::$assignments[$operator];

        return $assignment($left, $right);
    }
}
