<?php 

namespace Pinq\Parsing;

use Pinq\Expressions\Expression;

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
    public function getSignatureHash(\ReflectionFunctionAbstract $reflection);
    
    /**
     * @return Expression[]
     */
    public function parse(\ReflectionFunctionAbstract $reflection);
}