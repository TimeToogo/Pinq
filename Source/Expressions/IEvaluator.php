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
     * Evaluates the expression with the supplied variable table and returns the returned value.
     * The default variables from the context can be overridden in the first parameter.
     *
     * @param array|null $variableTable
     *
     * @return mixed
     * @throws \Pinq\PinqException if invalid variables are supplied in the variable table.
     */
    public function evaluate(array $variableTable = null);

    /**
     * Evaluates the expression with the supplied variable table and this object and
     * returns the returned value.
     * The default variables from the context can be overridden in the first parameter.
     *
     * @param object|null $thisObject
     * @param array|null  $variableTable
     *
     * @return mixed
     */
    public function evaluateWithNewThis($thisObject, array $variableTable = null);
}
