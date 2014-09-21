<?php

namespace Pinq\Expressions\Operators;

use Pinq\PinqException;

/**
 * The enum containing PHP unary operators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Unary
{
    //Arithmetic
    const PLUS = '+%s';
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

    /**
     * Performs the unary operation on the supplied value.
     *
     * @param string $operator
     * @param mixed  $value
     *
     * @return mixed
     * @throws PinqException
     */
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

        if (!isset(self::$unaryOperations[$operator])) {
            throw new PinqException('Unary operator \'%s\' is not supported', $operator);
        }

        $operation = self::$unaryOperations[$operator];

        return $operation($value);
    }
}
