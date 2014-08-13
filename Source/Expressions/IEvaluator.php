<?php
namespace Pinq\Expressions;


/**
 * Interface of the expression evaluation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IEvaluator
{
    /**
     * Gets the evaluation context.
     *
     * @return IEvaluationContext
     */
    public function getContext();

    /**
     * Gets the required variables for the evaluator.
     *
     * @return string[]
     */
    public function getRequiredVariables();

    /**
     * Evaluates the expression under the context and returns the returned value.
     * The default variable table from the context can be overridden in the first parameter.
     *
     * @param array|null $variableTable
     *
     * @return mixed
     */
    public function evaluate(array $variableTable = null);
}