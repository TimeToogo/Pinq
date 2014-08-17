<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\FunctionBase;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the expression parameter collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterCollection
{
    /**
     * @var array
     */
    protected $parameters = [];

    public function __construct()
    {

    }

    /**
     * @return ParameterCollection
     */
    public static function none()
    {
        return new self();
    }

    /**
     * Adds an expression parameter to the collection with the supplied context.
     *
     * @param string            $name
     * @param O\Expression      $expression
     * @param FunctionBase|null $context
     *
     * @return void
     */
    public function addExpression($name, O\Expression $expression, FunctionBase $context = null)
    {
        if($context !== null) {
            $this->parameters[$name] = [
                    $context->getParameterScopedVariableMap(),
                    $expression->asEvaluator($context->getEvaluationContext())
            ];
        } else {
            $this->parameters[$name] = [
                    [],
                    $expression->asEvaluator()
            ];
        }
    }

    /**
     * Returns a parameter collection context for the supplied function.
     *
     * @param FunctionBase $context
     *
     * @return ParameterCollectionContext
     */
    public function forFunction(FunctionBase $context)
    {
        return new ParameterCollectionContext($context, $this);
    }

    /**
     * Resolves the parameter values by evaluating the expressions
     * with the supplied values.
     *
     * @param IResolvedParameterRegistry $parameters
     *
     * @return mixed[]
     */
    public function resolveParameters(IResolvedParameterRegistry $parameters)
    {
        $resolvedParameters = [];
        foreach ($this->parameters as $name => $data) {
            /** @var $parameterMap string[] */
            /** @var $evaluator O\IEvaluator */
            list($parameterMap, $evaluator) = $data;

            $variableTable = [];
            foreach ($parameterMap as $parameter => $variable) {
                $variableTable[$variable] = $parameters[$parameter];
            }

            $resolvedParameters[$name] = $evaluator->evaluate($variableTable);
        }

        return $resolvedParameters;
    }
}