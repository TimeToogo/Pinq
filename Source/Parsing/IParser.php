<?php

namespace Pinq\Parsing;

/**
 * The IParser is just an abstraction for converting a function
 * to an expression tree.
 */
interface IParser
{
    /**
     * @return string
     */
    public function GetSignatureHash(\ReflectionFunctionAbstract $Reflection);
    
    /**
     * @return Expression[]
     */
    public function Parse(\ReflectionFunctionAbstract $Reflection);
}
