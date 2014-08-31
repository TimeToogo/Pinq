<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP binary operators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Binary
{
    //Arithmetic
    const ADDITION       = '+';
    const SUBTRACTION    = '-';
    const MULTIPLICATION = '*';
    const DIVISION       = '/';
    const MODULUS        = '%';
    const POWER          = '**';
    //Bitwise
    const BITWISE_AND = '&';
    const BITWISE_OR  = '|';
    const BITWISE_XOR = '^';
    const SHIFT_LEFT  = '<<';
    const SHIFT_RIGHT = '>>';
    //Logical
    const LOGICAL_AND              = '&&';
    const LOGICAL_OR               = '||';
    const EQUALITY                 = '==';
    const IDENTITY                 = '===';
    const INEQUALITY               = '!=';
    const NOT_IDENTICAL            = '!==';
    const LESS_THAN                = '<';
    const LESS_THAN_OR_EQUAL_TO    = '<=';
    const GREATER_THAN             = '>';
    const GREATER_THAN_OR_EQUAL_TO = '>=';
    //String
    const CONCATENATION = '.';
    //Type
    const IS_INSTANCE_OF = 'instanceof';
}
