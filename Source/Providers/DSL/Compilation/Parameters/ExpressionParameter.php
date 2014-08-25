<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\FunctionBase;
use Pinq\Queries\Functions\FunctionEvaluationContextFactory;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the expression parameter collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionParameter
{
    /**
     * @var O\IEvaluator
     */
    protected $evaluator;

    /**
     * @var FunctionEvaluationContextFactory|null
     */
    protected $contextFactory;

    /**
     * @var mixed
     */
    protected $data;

    public function __construct(O\Expression $expression, FunctionBase $function = null, $data = null)
    {
        if ($function !== null) {
            $this->contextFactory = $function->getEvaluationContextFactory();
            $this->evaluator      = $expression->asEvaluator($this->contextFactory->getEvaluationContext());
        } else {
            $this->evaluator = $expression->asEvaluator();
        }

        $this->data = $data;
    }

    /**
     * @return O\IEvaluator
     */
    public function getEvaluator()
    {
        return $this->evaluator;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param IResolvedParameterRegistry $parameters
     *
     * @return mixed
     */
    public function evaluate(IResolvedParameterRegistry $parameters)
    {
        if ($this->contextFactory === null) {
            return $this->evaluator->evaluate();
        }

        $resolvedContext = $this->contextFactory->getEvaluationContext($parameters);
        return $this->evaluator->evaluateWithNewThis($resolvedContext->getThis(), $resolvedContext->getVariableTable());
    }
}