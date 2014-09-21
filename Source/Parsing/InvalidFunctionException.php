<?php

namespace Pinq\Parsing;

use Pinq\PinqException;

/**
 * Exception for errors while converting a function into
 * an expression tree
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InvalidFunctionException extends PinqException
{
    /**
     * @param string                      $messageFormat
     * @param \ReflectionFunctionAbstract $reflection
     *
     * @return self
     */
    public static function invalidFunctionMessage($messageFormat, \ReflectionFunctionAbstract $reflection)
    {
        return self::construct(
                array_merge(
                        [
                                'Invalid function %s defined in %s lines %d-%d: ' . $messageFormat,
                                $reflection->getName(),
                                $reflection->getFileName(),
                                $reflection->getStartLine(),
                                $reflection->getEndLine()
                        ],
                        array_slice(func_get_args(), 2)
                )
        );
    }
}
