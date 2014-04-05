<?php

namespace Pinq\Parsing;

/**
 * The IParser is just an abstraction for converting a function
 * to an expression tree.
 */
interface IParser
{
    /**
     * @return Expression[]
     */
    public function Parse(\ReflectionFunctionAbstract $Reflection);
}
