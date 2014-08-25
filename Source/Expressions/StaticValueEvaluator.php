<?php

namespace Pinq\Expressions;

/**
 * Implementation of the expression evaluator using a
 * static return value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StaticValueEvaluator extends Evaluator
{
    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value, IEvaluationContext $context = null)
    {
        parent::__construct($context);

        $this->value = $value;
    }

    protected function doEvaluation(array $variableTable)
    {
        return $this->value;
    }

    protected function doEvaluationWithNewThis(array $variableTable, $newThis)
    {
        return $this->value;
    }
}
