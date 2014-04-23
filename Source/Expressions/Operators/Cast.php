<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP cast operators
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Cast
{
    const ARRAY_CAST = '(array) ';
    const BOOLEAN = '(bool) ';
    const DOUBLE = '(double) ';
    const INTEGER = '(int) ';
    const OBJECT = '(object) ';
    const STRING = '(string) ';
}
