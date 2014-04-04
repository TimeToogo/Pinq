<?php

namespace Pinq\Parsing;

interface IFunctionToExpressionTreeConverter
{
    /**
     * Converts a function to the equivalent expression tree.
     *
     * @return \Pinq\FunctionExpressionTree
     */
    public function Convert(callable $Function);
}
