<?php

namespace Pinq\Parsing;

/**
 * The parser interface is an abstraction over parsing a function
 * into an expression tree.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IParser
{
    /**
     * Gets a function reflection from the supplied function.
     *
     * @param callable $function
     *
     * @return IFunctionReflection
     */
    public function getReflection(callable $function);

    /**
     * Parses the supplied function and returns it's declaration and body structure.
     * The __LINE__ magic constant should be resolved to is correct value.
     *
     * @param IFunctionReflection $reflection
     *
     * @return IFunctionStructure
     * @throws InvalidFunctionException
     */
    public function parse(IFunctionReflection $reflection);
}
