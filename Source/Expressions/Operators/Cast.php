<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP cast operators
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Cast
{
    const ArrayCast = '(array) ';
    const Boolean = '(bool) ';
    const Double = '(double) ';
    const Integer = '(int) ';
    const Object = '(object) ';
    const String = '(string) ';
}
