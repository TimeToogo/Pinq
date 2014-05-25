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
    final public static function fromCallable(callable $function)
    {
        if (is_array($function)) {
            return new \ReflectionMethod($function[0], $function[1]);
        } elseif ($function instanceof \Closure) {
            return new \ReflectionFunction($function);
        } elseif (is_object($function)) {
            return new \ReflectionMethod($function, '__invoke');
        } else {
            $name = null;
            is_callable($function, false, $name);

            return new \ReflectionFunction($name);
        }
    }
    
    private static $supportsVariadicParameters = null;
    
    /**
     * @param \ReflectionFunctionAbstract $function
     * @return boolean
     */
    public static function isVariadic(\ReflectionFunctionAbstract $function)
    {
        if(self::$supportsVariadicParameters === null) {
            self::$supportsVariadicParameters = method_exists('\ReflectionParameter', 'isVariadic');
        }
        
        foreach($function->getParameters() as $parameter) {
            if($parameter->getName() === '...' || 
                    (self::$supportsVariadicParameters && $parameter->isVariadic())) {
                return true;
            }
        }
        
        return false;
    }
}
