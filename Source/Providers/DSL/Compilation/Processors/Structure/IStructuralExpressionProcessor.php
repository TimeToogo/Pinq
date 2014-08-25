<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters;

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
     * @param O\Expression $expression
     *
     * @return boolean
     */
    public function matches(O\Expression $expression);

    /**
     * Adds the necessary expression(s) to the supplied collection.
     *
     * @param O\Expression                           $expression The *matched* expression
     * @param Parameters\ExpressionCollectionContext $expressionCollection
     *
     * @return void
     */
    public function parameterize(
            O\Expression $expression,
            Parameters\ExpressionCollectionContext $expressionCollection
    );

    /**
     * Gets a unique hash of the *parameterized* expression from the supplied registry.
     *
     * @param O\Expression                          $expression The *parameterized* expression
     * @param Parameters\ResolvedExpressionRegistry $expressionRegistry
     *
     * @return string
     */
    public function hash(O\Expression $expression, Parameters\ResolvedExpressionRegistry $expressionRegistry);

    /**
     * Updates the *matched* expression with it's resolved value from
     * the supplied registry.
     *
     * @param O\Expression                          $expression The *matched* expression
     * @param Parameters\ResolvedExpressionRegistry $expressionRegistry
     *
     * @return O\Expression
     */
    public function inline(O\Expression $expression, Parameters\ResolvedExpressionRegistry $expressionRegistry);
}