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
    //Arthmetic
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
}
