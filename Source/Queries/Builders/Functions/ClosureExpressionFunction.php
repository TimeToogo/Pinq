<?php

namespace Pinq\Queries\Builders\Functions;

use Pinq\Expressions as O;

/**
 * Query parameter of a function represented by a closure expression tree.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClosureExpressionFunction extends BaseFunction
{
    /**
     * @var O\ClosureExpression
     */
    private $expression;

    /**
     * @var O\IEvaluationContext|null
     */
    private $evaluationContext;

    /**
     * @var callable|null
     */
    private $callable;

    public function __construct($id, O\ClosureExpression $expression, O\IEvaluationContext $evaluationContext = null)
    {
        parent::__construct($id);
        $this->expression        = $expression;
        $this->evaluationContext = $evaluationContext;
    }

    public function getType()
    {
        return self::CLOSURE_EXPRESSION;
    }

    /**
     * @return O\ClosureExpression
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return boolean
     */
    public function hasEvaluationContext()
    {
        return $this->evaluationContext !== null;
    }

    /**
     * @return O\IEvaluationContext|null
     */
    public function getEvaluationContext()
    {
        return $this->evaluationContext;
    }

    public function getCallable()
    {
        if ($this->callable === null) {
            $this->callable = $this->expression->evaluate($this->evaluationContext);
        }

        return $this->callable;
    }
}
