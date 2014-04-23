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
    private function __construct()
    {
        
    }
    
    /**
     * @param callable $function
     * @return \ReflectionFunctionAbstract
     */
    public static final function fromCallable(callable $function)
    {
        if (is_array($function)) {
            return new \ReflectionMethod($function[0], $function[1]);
        }
        else if ($function instanceof \Closure) {
            return new \ReflectionFunction($function);
        }
        else if (is_object($function)) {
            return new \ReflectionMethod($function, '__invoke');
        }
        else {
            $name = null;
            is_callable($function, false, $name);
            
            return new \ReflectionFunction($name);
        }
    }
}