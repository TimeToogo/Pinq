<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\FunctionEvaluationContextFactory;
use Pinq\Queries\Functions\IFunction;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the expression parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionParameter extends QueryParameterBase
{
    /**
     * @var O\IEvaluator
     */
    protected $evaluator;

    /**
     * @var FunctionEvaluationContextFactory|null
     */
    protected $contextFactory;

    public function __construct(O\Expression $expression, IParameterHasher $hasher, IFunction $function = null, $data = null)
    {
        parent::__construct($hasher, $data);
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

    public function doEvaluate(IResolvedParameterRegistry $parameters)
    {
        if ($this->contextFactory === null) {
            return $this->evaluator->evaluate();
        }

        $resolvedContext = $this->contextFactory->getEvaluationContext($parameters);

        return $this->evaluator->evaluateWithNewThis($resolvedContext->getThis(), $resolvedContext->getVariableTable());
    }
}
