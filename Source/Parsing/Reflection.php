<?php

namespace Pinq\Parsing;

/**
 * Utility class for getting the reflection from any type
 * of callable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Reflection
{
    private function __construct()
    {

    }

    /**
     * @param callable $function
     *
     * @return \ReflectionFunctionAbstract
     * @throws InvalidFunctionException
     */
    final public static function fromCallable(callable $function)
    {
        // If is array it could be an instance or static method:
        // ['class', 'method'] or [$instance, 'method'];
        if (is_array($function)) {
            return new \ReflectionMethod($function[0], $function[1]);
        }
        // If it is a closure it could be an actual closure or
        // possibly a closure of another function from
        // \ReflectionFunction::getClosure or \ReflectionMethod::getClosure
        elseif ($function instanceof \Closure) {
            $reflection = new \ReflectionFunction($function);

            // If the name is {closure} it as an actual closure
            if ($reflection->getShortName() === '{closure}') {
                return $reflection;
            }
            // Bail out, no (sane) way of determining the actual function
            // represented by the closure
            throw InvalidFunctionException::invalidFunctionMessage(
                    'The function has been wrapped in closure '
                    . '(most likely  via ReflectionFunction::getClosure or \ReflectionMethod::getClosure) '
                    . 'and this is not supported',
                    $reflection);
        }
        // If an object but not a closure it must be an object defining
        // the __invoke magic method.
        elseif (is_object($function)) {
            return new \ReflectionMethod($function, '__invoke');
        }
        // Fallback to function
        else {
            $name = null;
            is_callable($function, false, $name);

            return new \ReflectionFunction($name);
        }
    }

    private static $supportsVariadicParameters = null;

    /**
     * @param \ReflectionFunctionAbstract $function
     *
     * @return boolean
     */
    public static function isVariadic(\ReflectionFunctionAbstract $function)
    {
        if (self::$supportsVariadicParameters === null) {
            self::$supportsVariadicParameters = method_exists('\ReflectionParameter', 'isVariadic');
        }

        foreach ($function->getParameters() as $parameter) {
            if ($parameter->getName() === '...' ||
                    (self::$supportsVariadicParameters && $parameter->isVariadic())
            ) {
                return true;
            }
        }

        return false;
    }
}
