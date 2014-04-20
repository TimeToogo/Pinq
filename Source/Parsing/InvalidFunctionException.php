<?php

namespace Pinq\Parsing;

class InvalidFunctionException extends \Pinq\PinqException
{
    /**
     * @param string $MessageFormat
     */
    public static function InvalidFunctionMessage($MessageFormat, \ReflectionFunctionAbstract $Reflection)
    {
        return self::Construct(array_merge([
            'Invalid function %s defined in %s lines %d-%d: ' . $MessageFormat,
            $Reflection->getName(),
            $Reflection->getFileName(),
            $Reflection->getStartLine(),
            $Reflection->getEndLine()],
            array_slice(func_get_args(), 2)));
    }
    
    public static function MustContainValidReturnExpression(\ReflectionFunctionAbstract $Reflection)
    {
         return self::InvalidFunctionMessage('must contain a valid return statement', $Reflection);
    }
}
