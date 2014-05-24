<?php

namespace Pinq\Iterators\Utilities;

/**
 * Utility methods for allow internal functions to have excessive arguments.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Functions
{
    private function __construct()
    {

    }
    
    /**
     * Returns a wrapper function that will allow the function to be called
     * with excessive number of arguments, this is useful for internal function support.
     * Note that references will not be maintained.
     * 
     * @param callable $function
     * @return callable
     */
    public static function allowExcessiveArguments(callable $function)
    {
        if($function instanceof \Pinq\FunctionExpressionTree) {
            $function = $function->getCompiledFunction();
        }
        //Closures already allow excessive arguments
        if($function instanceof \Closure) {
            return $function;
        }
        
        $reflection = \Pinq\Parsing\Reflection::fromCallable($function);
        if($reflection->isUserDefined()) {
            return $function;
        }
        
        if(\Pinq\Parsing\Reflection::isVariadic($reflection)) {
            return $function;
        }
        
        $numberOfArguments = $reflection->getNumberOfParameters();
        switch($numberOfArguments) {
            case 0:
                return function () use ($function) { return $function(); }; 
                
            case 1:
                return function ($a) use ($function) { return $function($a); }; 
                
            case 2:
                return function ($a, $b) use ($function) { return $function($a, $b); }; 
                
            case 3:
                return function ($a, $b, $c) use ($function) { return $function($a, $b, $c); }; 
                
            case 4:
                return function ($a, $b, $c, $d) use ($function) { return $function($a, $b, $c, $d); }; 
                
            default:
                return function () use ($function, $numberOfArguments) { 
                    return call_user_func_array($function, array_slice(func_get_args(), 0, $numberOfArguments));
                }; 
        }
    }
}
