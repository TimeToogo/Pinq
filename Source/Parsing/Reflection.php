<?php

namespace Pinq\Parsing;

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
