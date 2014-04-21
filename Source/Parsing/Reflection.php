<?php

namespace Pinq\Parsing;

/**
 * Utility class for getting the reflection from any
 * type of callable.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Reflection
{
    private function __construct() {}
    
    /**
     * @param callable $Function
     * @return \ReflectionFunctionAbstract
     */
    final public static function FromCallable(callable $Function)
    {
        if (is_array($Function)) {
            return new \ReflectionMethod($Function[0], $Function[1]);
        } 
        else if ($Function instanceof \Closure) {
            return new \ReflectionFunction($Function);
        } 
        else if (is_object($Function)) {
            return new \ReflectionMethod($Function, '__invoke');
        } 
        else {
            $Name = null;
            is_callable($Function, false, $Name);

            return new \ReflectionFunction($Name);
        }
    }
}
