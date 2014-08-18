<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Base class of a function structure.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class FunctionBase implements \Serializable
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
     * The structure of the function's parameters
     *
     * @var Parameters\ParameterBase
     */
    protected $parameters;

    /**
     * The expressions of the body statements of the function
     *
     * @var O\Expression[]||null
     */
    protected $bodyExpressions = [];

    final public function __construct(
            $callableId,
            $scopeType,
            $namespace,
            array $parameterScopedVariableMap,
            array $parameterExpressions,
            array $bodyExpressions = null
    ) {
        $this->callableId                 = $callableId;
        $this->scopeType                  = $scopeType;
        $this->namespace                  = $namespace;
        $this->parameterScopedVariableMap = $parameterScopedVariableMap;
        $this->parameters                 = $this->getParameterStructure($parameterExpressions);
        $this->bodyExpressions            = $bodyExpressions;

        $this->initialize();
    }

    protected function initialize()
    {

    }

    /**
     * @param O\ParameterExpression[] $parameterExpressions
     *
     * @return Parameters\ParameterBase
     */
    abstract protected function getParameterStructure(array $parameterExpressions);

    /**
     * Gets a callable factory for the function structure.
     *
     * @return callable
     */
    public static function factory()
    {
        $static = get_called_class();

        return function (
                $callableParameter,
                $scopeType,
                $namespace,
                array $parameterScopedVariableMap,
                array $parameterExpressions,
                array $bodyExpressions = null
        ) use ($static) {
            return new $static(
                    $callableParameter,
                    $scopeType,
                    $namespace,
                    $parameterScopedVariableMap,
                    $parameterExpressions,
                    $bodyExpressions);
        };
    }

    /**
     * Gets the parameter id of the callable for the function.
     *
     * @return string
     */
    final public function getCallableId()
    {
        return $this->callableId;
    }

    /**
     * Whether the function has a scoped type.
     *
     * @return boolean
     */
    public function hasScopeType()
    {
        return $this->scopeType !== null;
    }

    /**
     * Gets the bound type of the function.
     * Null if there is no bound type.
     *
     * @return string|null
     */
    public function getScopeType()
    {
        return $this->scopeType;
    }

    /**
     * Whether the function is defined in a namespace.
     *
     * @return boolean
     */
    public function hasNamespace()
    {
        return $this->namespace !== null;
    }

    /**
     * Gets the namespace the function was defined in.
     * Null if was defined in the global namespace.
     *
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return boolean
     */
    public function isInternal()
    {
        return $this->bodyExpressions === null;
    }

    final protected function verifyNotInternal($method)
    {
        if ($this->isInternal()) {
            throw new \Pinq\PinqException(
                    'Invalid call to %s::%s: function is not user defined.',
                    get_class($this),
                    $method);
        }
    }

    /**
     * Gets an array containing the parameter ids as keys with their
     * respective scoped variable name as the value.
     *
     * @return array<string, string>
     */
    public function getParameterScopedVariableMap()
    {
        return $this->parameterScopedVariableMap;
    }

    final public function serialize()
    {
        return serialize(
                [
                        $this->callableId,
                        $this->scopeType,
                        $this->namespace,
                        $this->parameterScopedVariableMap,
                        $this->parameters,
                        $this->bodyExpressions,
                        $this->dataToSerialize()
                ]
        );
    }

    protected function dataToSerialize()
    {

    }

    final public function unserialize($data)
    {
        list(
                $this->callableId,
                $this->scopeType,
                $this->namespace,
                $this->parameterScopedVariableMap,
                $this->parameters,
                $this->bodyExpressions,
                $data) = unserialize($data);
        $this->unserializeData($data);
    }

    protected function unserializeData($data)
    {

    }

    /**
     * Gets the body expressions of the function.
     *
     * @return O\Expression[]
     * @throws \Pinq\PinqException if the function is internal
     */
    final public function getBodyExpressions()
    {
        $this->verifyNotInternal(__FUNCTION__);
        return $this->bodyExpressions;
    }

    /**
     * Gets the body expressions of the function before and including
     * the first return statement.
     *
     * @return O\Expression[]
     * @throws \Pinq\PinqException if the function is internal
     */
    final public function getBodyExpressionsUntilReturn()
    {
        $this->verifyNotInternal(__FUNCTION__);
        $expressions = [];
        foreach ($this->bodyExpressions as $expression) {
            $expressions[] = $expression;
            if ($expression instanceof O\ReturnExpression) {
                break;
            }
        }

        return $expressions;
    }

    /**
     * Gets amount of body expressions of the function.
     *
     * @return int
     * @throws \Pinq\PinqException if the function is internal
     */
    final public function countBodyExpressions()
    {
        $this->verifyNotInternal(__FUNCTION__);
        return count($this->bodyExpressions);
    }

    /**
     * Gets amount of body expressions of the function before and including
     * the first return statement.
     *
     * @return int
     * @throws \Pinq\PinqException if the function is internal
     */
    final public function countBodyExpressionsUntilReturn()
    {
        $this->verifyNotInternal(__FUNCTION__);
        return count($this->getBodyExpressionsUntilReturn());
    }

    /**
     * @return Parameters\ParameterBase
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Gets an array containing default values indexed by their
     * respective unused parameter name.
     * This is useful as it will introduce variables in the scope of the
     * function that may be validly used.
     *
     * @return array<string, mixed>
     */
    public function getUnusedParameterDefaultValueMap()
    {
        $defaultValueMap = [];
        foreach ($this->parameters->getUnusedParameterDefaultMap() as $name => $defaultValueExpression) {
            if ($defaultValueExpression !== null) {
                /** @var $defaultValueExpression O\Expression */
                $defaultValueMap[$name] = $defaultValueExpression->evaluate(
                        O\EvaluationContext::staticContext($this->namespace, $this->scopeType)
                );
            }
        }

        return $defaultValueMap;
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
        if ($parameters !== null) {
            foreach ($this->parameterScopedVariableMap as $parameter => $variableName) {
                if ($variableName === 'this') {
                    $thisObject = $parameters[$parameter];
                } else {
                    $variableTable[$variableName] = $parameters[$parameter];
                }
            }
        }
        $variableTable = $variableTable + $this->getUnusedParameterDefaultValueMap();

        return new O\EvaluationContext($this->namespace, $this->scopeType, $thisObject, $variableTable);
    }

    /**
     * @param string|null             $scopeType
     * @param string|null             $namespace
     * @param string[]                $parameterScopedVariableMap
     * @param O\ParameterExpression[] $parameterExpressions
     * @param O\Expression[]|null     $bodyExpressions
     *
     * @return static
     */
    public function update(
            $scopeType,
            $namespace,
            array $parameterScopedVariableMap,
            array $parameterExpressions,
            array $bodyExpressions = null
    ) {
        if ($this->scopeType === $scopeType
                && $this->namespace === $namespace
                && $this->parameterScopedVariableMap === $parameterScopedVariableMap
                && $this->parameters->getAll() === $parameterExpressions
                && $this->bodyExpressions === $bodyExpressions
        ) {
            return $this;
        }

        return new static(
                $this->callableId,
                $scopeType,
                $namespace,
                $parameterScopedVariableMap,
                $parameterExpressions,
                $bodyExpressions);
    }

    /**
     * @param O\Expression[]|null $bodyExpressions
     *
     * @return static
     */
    public function updateBody(array $bodyExpressions = null)
    {
        return $this->update(
                $this->scopeType,
                $this->namespace,
                $this->parameterScopedVariableMap,
                $this->parameters->getAll(),
                $bodyExpressions
        );
    }
}
