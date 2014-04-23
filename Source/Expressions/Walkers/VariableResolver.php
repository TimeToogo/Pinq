<?php

namespace Pinq\Expressions\Walkers;

use Pinq\Expressions as O;

/**
 * Resolves variables within the expression tree to the supplied expressions/values.
 *
 * $Var = 4 + 5 - $Unresolvable;
 * === with ['Unresolvable' => 97] resolves to ===
 * $Var = 4 + 5 - 97;
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class VariableResolver extends O\ExpressionWalker
{
    /**
     * The array containing the variable name and the value to expression
     * to replace it with
     *
     * @var array<string, O\Expression>
     */
    private $variableExpressionMap = [];

    public function __construct(array $variableExpressionMap = [])
    {
        $this->variableExpressionMap = $variableExpressionMap;
    }

    /**
     * Sets which variables to replace with what
     *
     * @param array<string, O\Expression> $variableExpressionMap
     */
    public function setVariableExpressionMap(array $variableExpressionMap)
    {
        $this->variableExpressionMap = $variableExpressionMap;
    }

    /*
     * Resolves scoped the variables in closures
     */
    public function walkClosure(O\ClosureExpression $expression)
    {
        $originalVariableExpressionMap = $this->variableExpressionMap;
        $usedVariableNames = $expression->getUsedVariableNames();
        //Filter to only used values
        $this->variableExpressionMap = array_intersect_key($this->variableExpressionMap, array_flip(array_values($usedVariableNames)) + ['this' => null]);
        //Include $this variable scope
        $expression =
                $expression->update(
                        $expression->getParameterExpressions(),
                        array_diff($usedVariableNames, array_keys($this->variableExpressionMap)),
                        $this->walkAll($expression->getBodyExpressions()));
        //Restore parent scope with all variables values
        $this->variableExpressionMap = $originalVariableExpressionMap;

        return $expression;
    }

    /*
     * Replace the variable with the value expression of the current scope
     */
    public function walkVariable(O\VariableExpression $expression)
    {
        $nameExpression = $this->walk($expression->getNameExpression())->simplify();

        if ($nameExpression instanceof O\ValueExpression) {
            $name = $nameExpression->getValue();

            if (isset($this->variableExpressionMap[$name])) {
                return $this->variableExpressionMap[$name];
            }
        }

        return $expression;
    }
}
