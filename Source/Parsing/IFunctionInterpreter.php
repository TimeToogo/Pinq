<?php

namespace Pinq\Parsing;

/**
 * Interface for loading reflection and expression data from functions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionInterpreter
{
    /**
     * Gets the reflection for the supplied function.
     *
     * @param callable $function
     *
     * @return IFunctionReflection
     */
    public function getReflection(callable $function);

    /**
     * Gets the structure for the supplied function.
     * Magic constants (__DIR__, __FILE__...) will be resolved to their appropriate value.
     * Scoped class keywords/constants (static::, self::, parent::) will be resolved
     * to their fully qualified name.
     *
     * @param IFunctionReflection $reflection
     *
     * @return IFunctionStructure
     * @throws InvalidFunctionException
     */
    public function getStructure(IFunctionReflection $reflection);
}
