<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP unary operators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Unary
{
    //Arithmetic
    const PLUS = '+%s';
    //Usless
    const NEGATION      = '-%s';
    const INCREMENT     = '%s++';
    const DECREMENT     = '%s--';
    const PRE_INCREMENT = '++%s';
    const PRE_DECREMENT = '--%s';
    //Bitwise
    const BITWISE_NOT = '~%s';
    //Logical
    const NOT = '!%s';

    private static $unaryOperations;

    public static function doUnaryOperation($operator, &$value)
    {
        if (self::$unaryOperations === null) {
            self::$unaryOperations = [
                    self::BITWISE_NOT   => function (&$i) { return ~$i; },
                    self::NOT           => function (&$i) { return !$i; },
                    self::INCREMENT     => function (&$i) { return $i++; },
                    self::DECREMENT     => function (&$i) { return $i--; },
                    self::PRE_INCREMENT => function (&$i) { return ++$i; },
                    self::PRE_DECREMENT => function (&$i) { return --$i; },
                    self::NEGATION      => function (&$i) { return -$i; },
                    self::PLUS          => function (&$i) { return +$i; },
            ];
        }

        $operation = self::$unaryOperations[$operator];

        return $operation($value);
    }
}
