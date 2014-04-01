<?php

namespace Pinq\Parsing;

interface IFunctionToExpressionTreeConverter
{
    /**
     * Converts a function to the equivalent expression tree.
     *
     * @return FunctionExpressionTree
     */
    public function Convert(callable $Function);
}
