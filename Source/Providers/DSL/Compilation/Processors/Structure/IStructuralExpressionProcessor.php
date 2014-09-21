<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Queries\Functions\IFunction;

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
     * @param IFunction    $function
     * @param O\Expression $expression
     *
     * @return boolean
     */
    public function matches(
            IFunction $function,
            O\Expression $expression
    );

    /**
     * Adds the necessary expression(s) to the supplied collection.
     *
     * @param IFunction                      $function
     * @param O\Expression                   $expression
     * @param Parameters\ParameterCollection $parameters
     *
     * @return void
     */
    public function parameterize(
            IFunction $function,
            O\Expression $expression,
            Parameters\ParameterCollection $parameters
    );

    /**
     * Updates the matched expression with it's resolved value from
     * the supplied registry.
     *
     * @param IFunction                            $function
     * @param O\Expression                         $expression
     * @param Parameters\ResolvedParameterRegistry $parameters
     *
     * @return O\Expression
     */
    public function inline(
            IFunction $function,
            O\Expression $expression,
            Parameters\ResolvedParameterRegistry $parameters
    );
}
