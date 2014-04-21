<?php

namespace Pinq\Parsing;

use \Pinq\Expressions\Expression;

/**
 * The IParser is an abstraction for converting a function
 * to an expression tree.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
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
