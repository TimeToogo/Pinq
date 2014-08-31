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
    const NEGATION      = '-%s';
    const INCREMENT     = '%s++';
    const DECREMENT     = '%s--';
    const PRE_INCREMENT = '++%s';
    const PRE_DECREMENT = '--%s';
    //Bitwise
    const BITWISE_NOT = '~%s';
    //Logical
    const NOT = '!%s';
}
