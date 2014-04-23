<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP unary operators
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Unary
{
    //Arithmetic
    const PLUS = '+%s';
    //Usless
    const NEGATION = '-%s';
    const INCREMENT = '%s++';
    const DECREMENT = '%s--';
    const PRE_INCREMENT = '++%s';
    const PRE_DECREMENT = '--%s';
    //Bitwise
    const BITWISE_NOT = '~%s';
    //Logical
    const NOT = '!%s';
}
