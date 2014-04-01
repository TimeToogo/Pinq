<?php

namespace Pinq\Expressions\Operators;

/**
 * The class containing php assignment operators
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Assignment
{
    const Equal = '=';
    const EqualReference = '=&';

    //Arthmetic
    const Addition = '+=';
    const Subtraction = '-=';
    const Multiplication = '*=';
    const Division = '/=';
    const Modulus = '%=';

    //Bitwise
    const BitwiseAnd = '&=';
    const BitwiseOr = '|=';
    const BitwiseXor = '^=';
    const ShiftLeft = '<<=';
    const ShiftRight = '>>=';

    //String
    const Concatenate = '.=';
}
