<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Interface of the structural expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IStructuralExpressionProcessor
{
    /**
     * Whether the supplied expression should be processed as structural expression.
     *
     * @param FunctionBase $function
     * @param O\Expression $expression
     *
     * @return boolean
     */
    public function matches(
            FunctionBase $function,
            O\Expression $expression
    );

    /**
     * Adds the necessary expression(s) to the supplied collection.
     *
     * @param FunctionBase                   $function
     * @param O\Expression                   $expression
     * @param Parameters\ParameterCollection $parameters
     *
     * @return void
     */
    public function parameterize(
            FunctionBase $function,
            O\Expression $expression,
            Parameters\ParameterCollection $parameters
    );

    /**
     * Updates the matched expression with it's resolved value from
     * the supplied registry.
     *
     * @param FunctionBase                         $function
     * @param O\Expression                         $expression
     * @param Parameters\ResolvedParameterRegistry $parameters
     *
     * @return O\Expression
     */
    public function inline(
            FunctionBase $function,
            O\Expression $expression,
            Parameters\ResolvedParameterRegistry $parameters
    );
}