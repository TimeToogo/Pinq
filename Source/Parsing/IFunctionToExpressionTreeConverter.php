<?php

namespace Pinq\Parsing;

/**
 * Facade for converting a function into the equivalent expression tree
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IFunctionToExpressionTreeConverter
{
    /**
     * Converts a function to the equivalent expression tree.
     *
     * @return \Pinq\FunctionExpressionTree
     */
    public function convert(callable $function);
}
