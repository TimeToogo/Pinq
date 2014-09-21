<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the evaluation context factory.
 * Contains the necessary data to build an evaluation context of the original function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionEvaluationContextFactory
{
    /**
     * @var string
     */
    protected $callableId;

    /**
     * @var string|null
     */
    protected $scopeType;

    /**
     * @var string|null
     */
    protected $namespace;

    /**
     * Array containing the scoped variable names of the function indexed
     * by their respective parameter names.
     *
     * @var array<string, string>
     */
    protected $parameterScopedVariableMap;

    /**
     * Array of the unused parameter's default value expression indexed by their
     * parameter name.
     *
     * @var O\IEvaluator[]
     */
    protected $unusedParameterDefaultMap = [];

    public function __construct(
            $namespace,
            $scopeType,
            $callableId,
            array $parameterScopedVariableMap,
            array $unusedParameterDefaultExpressionMap
    ) {
        $this->callableId                 = $callableId;
        $this->namespace                  = $namespace;
        $this->parameterScopedVariableMap = $parameterScopedVariableMap;
        $this->scopeType                  = $scopeType;
        foreach ($unusedParameterDefaultExpressionMap as $parameter => $expression) {
            if ($expression !== null) {
                /** @var $expression O\Expression */
                $this->unusedParameterDefaultMap[$parameter] = $expression->asEvaluator(
                        O\EvaluationContext::staticContext($this->namespace, $this->scopeType)
                );
            }
        }
    }

    /**
     * @return boolean
     */
    public function hasNamespace()
    {
        return $this->namespace !== null;
    }

    /**
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return boolean
     */
    public function hasScopeType()
    {
        return $this->scopeType !== null;
    }

    /**
     * @return string|null
     */
    public function getScopeType()
    {
        return $this->scopeType;
    }

    /**
     * @return array<string, string>
     */
    public function getParameterScopedVariableMap()
    {
        return $this->parameterScopedVariableMap;
    }

    /**
     * @return string
     */
    public function getCallableId()
    {
        return $this->callableId;
    }

    /**
     * Gets an evaluation context for function with the resolved parameters.
     *
     * @param IResolvedParameterRegistry|null $parameters
     *
     * @return O\IEvaluationContext
     */
    public function getEvaluationContext(IResolvedParameterRegistry $parameters = null)
    {
        $thisObject    = null;
        $variableTable = array_fill_keys($this->parameterScopedVariableMap, null);
        unset($variableTable['this']);

        if ($parameters !== null) {
            foreach ($this->parameterScopedVariableMap as $parameter => $variableName) {
                if ($variableName === 'this') {
                    $thisObject = $parameters[$parameter];
                } else {
                    $variableTable[$variableName] = $parameters[$parameter];
                }
            }
        }

        foreach ($this->unusedParameterDefaultMap as $name => $evaluator) {
            $variableTable[$name] = $evaluator->evaluate();
        }

        return new O\EvaluationContext($this->namespace, $this->scopeType, $thisObject, $variableTable);
    }
}
