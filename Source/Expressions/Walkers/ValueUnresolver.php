<?php

namespace Pinq\Expressions\Walkers;

use Pinq\Expressions as O;

/**
 * Unresolves and stores any values in the expression tree and replaces them with uncolliding variable names.
 * This is useful when compiling an expression tree and certain values shouldn't be stored inline, such as
 * objects or arrays.
 *
 * 3 + $Var;
 * === will become ===
 * ${'some unclashing name'} + $Var
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ValueUnresolver extends O\ExpressionWalker
{
    /**
     * The current amount of variables that have be inserted
     *
     * @var int
     */
    private $variableCount = 0;

    /**
     * An array with the variable names as key and the
     * respective value as the value
     *
     * @var array<string, mixed>
     */
    private $variableNameValueMap = [];

    /**
     * Determines whether the value should be replaced a variable
     *
     * @var callable| null
     */
    private $valueFilterFunction;

    public function __construct(callable $valueFilterFunction = null)
    {
        $this->valueFilterFunction = $valueFilterFunction;
    }

    /**
     * Returns the variable value map
     *
     * @return array<string, mixed>
     */
    public function getVariableNameValueMap()
    {
        return $this->variableNameValueMap;
    }

    /**
     * @return void
     */
    public function resetVariableNameValueMap()
    {
        $this->variableCount = 0;
        $this->variableNameValueMap = [];
    }

    /**
     * Sets the function to filter which values should not be inlined
     *
     * @param callable $valueFilterFunction The filter function
     */
    public function setValueFilter(callable $valueFilterFunction)
    {
        $this->valueFilterFunction = $valueFilterFunction;
    }

    private function makeVariableName()
    {
        return '___' . ++$this->variableCount . '___';
    }

    /**
     * Walks the and finds any values in the body then adds them as used variables
     *
     * @param O\ClosureExpression $expression
     * @return O\ClosureExpression
     */
    public function walkClosure(O\ClosureExpression $expression)
    {
        $walkedBodyExpressions = $this->walkAll($expression->getBodyExpressions());

        return $expression->update(
                $expression->getParameterExpressions(),
                array_merge($expression->getUsedVariableNames(), array_keys($this->variableNameValueMap)),
                $walkedBodyExpressions);
    }

    public function walkValue(O\ValueExpression $expression)
    {
        $valueFilter = $this->valueFilterFunction;
        $value = $expression->getValue();

        if ($valueFilter === null || $valueFilter($value)) {
            $variableName = array_search($value, $this->variableNameValueMap, true);

            if ($variableName === false) {
                $variableName = $this->makeVariableName();
                $this->variableNameValueMap[$variableName] = $value;
            }

            return O\Expression::variable(O\Expression::value($variableName));
        }

        return $expression;
    }
}
