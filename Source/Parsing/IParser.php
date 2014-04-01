<?php

namespace Pinq\Parsing;

/**
 * The IParser is just an abstraction for converting a function to an ast structure
 * which can than be converted to an expression tree.
 */
interface IParser
{
    /**
     * @return IAST
     */
    public function Parse(\ReflectionFunctionAbstract $Reflection);
}
