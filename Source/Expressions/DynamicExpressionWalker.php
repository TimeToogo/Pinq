<?php

namespace Pinq\Expressions;

/**
 * Implementation of the expression walker takes
 * an array of callables and delegates walking the expressions
 * to those callables.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DynamicExpressionWalker extends ExpressionWalker
{
    /**
     * @var array<string, callable>
     */
    protected $callableMap;

    public function __construct(array $callableMap)
    {
        $this->callableMap = $callableMap;
    }

    protected function doWalk(Expression $expression)
    {
        $expressionType = get_class($expression);

        do {
            if (isset($this->callableMap[$expressionType])) {
                return $this->callableMap[$expressionType]($expression, $this);
            }
        } while (($expressionType = get_parent_class($expressionType)) !== false
                && $expressionType !== Expression::EXPRESSION_TYPE);

        return $expression->traverse($this);
    }
}
