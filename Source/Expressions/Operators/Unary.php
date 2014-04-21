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
    const Plus = '+%s';//Usless
    const Negation = '-%s';
    const Increment = '%s++';
    const Decrement = '%s--';
    const PreIncrement = '++%s';
    const PreDecrement = '--%s';

    //Bitwise
    const BitwiseNot = '~%s';

    //Logical
    const Not = '!%s';
}
