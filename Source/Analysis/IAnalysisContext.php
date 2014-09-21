<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Interface of the static type analysis context.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IAnalysisContext extends ITyped
{
    /**
     * Gets the evaluation context.
     *
     * @return O\IEvaluationContext
     */
    public function getEvaluationContext();

    /**
     * Gets the type of the expression.
     * Null if no type has been set.
     * The expression is compared using value equality (same code).
     *
     * @param O\Expression $expression
     *
     * @return IType|null
     */
    public function getExpressionType(O\Expression $expression);

    /**
     * Sets the type of the expression.
     *
     * @param O\Expression $expression
     * @param IType        $type
     *
     * @return void
     */
    public function setExpressionType(O\Expression $expression, IType $type);

    /**
     * Removes the type of the expression.
     *
     * @param O\Expression $expression
     *
     * @return void
     */
    public function removeExpressionType(O\Expression $expression);

    /**
     * Creates a reference between the supplied expressions.
     *
     * @param O\Expression $expression
     * @param O\Expression $referencedExpression
     *
     * @return void
     */
    public function createReference(O\Expression $expression, O\Expression $referencedExpression);

    /**
     * Creates a new analysis context with an empty expression type list.
     *
     * @return IAnalysisContext
     */
    public function inNewScope();
}
