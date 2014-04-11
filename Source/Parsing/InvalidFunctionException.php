<?php

namespace Pinq\Parsing;

class InvalidFunctionException extends \Pinq\PinqException
{
    public static function ContainsUnresolvableVariables(\ReflectionFunctionAbstract $Reflection, array $UnresolvableVariables)
    {
        return self::InvalidFunctionMessage('contains unresolvable variables: $%s',
                $Reflection,
                implode(', $', $UnresolvableVariables));
    }

    public static function MustContainValidReturnExpression(\ReflectionFunctionAbstract $Reflection)
    {
         return self::InvalidFunctionMessage('must contain a valid return statement', $Reflection);
    }

    public static function InvalidFunctionSignature(\ReflectionFunctionAbstract $Reflection, array $ParameterTypeHints = [])
    {
        return self::InvalidFunctionMessage('function has an invalid signature, expecting %s parameter(s) with types %s, %d given with types %s',
                $Reflection,
                count($ParameterTypeHints),
                implode(', ', $ParameterTypeHints),
                $Reflection->getNumberOfParameters(),
                implode(', ', array_map(function ($I) { return $I->getClass() ? $I->getClass()->getName() : '{NONE}'; }, $Reflection->getParameters())));
    }

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
}
