<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\PinqException;

/**
 * Base class of a function structure.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class FunctionBase implements \Serializable
{
    /**
     * @var FunctionEvaluationContextFactory
     */
    protected $evaluationContextFactory;

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
        $this->parameters               = $this->getParameterStructure($parameterExpressions);
        $this->bodyExpressions          = $bodyExpressions;
        $this->evaluationContextFactory =
                new FunctionEvaluationContextFactory(
                        $namespace,
                        $scopeType,
                        $callableId,
                        $parameterScopedVariableMap,
                        $this->parameters->getUnusedParameterDefaultMap());

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
        return $this->evaluationContextFactory->getCallableId();
    }

    /**
     * Whether the function has a scoped type.
     *
     * @return boolean
     */
    public function hasScopeType()
    {
        return $this->evaluationContextFactory->hasScopeType();
    }

    /**
     * Gets the bound type of the function.
     * Null if there is no bound type.
     *
     * @return string|null
     */
    public function getScopeType()
    {
        return $this->evaluationContextFactory->getScopeType();
    }

    /**
     * Whether the function is defined in a namespace.
     *
     * @return boolean
     */
    public function hasNamespace()
    {
        return $this->evaluationContextFactory->hasNamespace();
    }

    /**
     * Gets the namespace the function was defined in.
     * Null if was defined in the global namespace.
     *
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->evaluationContextFactory->getNamespace();
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
            throw new PinqException(
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
        return $this->evaluationContextFactory->getParameterScopedVariableMap();
    }

    final public function serialize()
    {
        return serialize(
                [
                        $this->evaluationContextFactory,
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
                $this->evaluationContextFactory,
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
     * @throws PinqException  if the function is internal
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
     * @throws PinqException  if the function is internal
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
     * @throws PinqException if the function is internal
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
     * @throws PinqException if the function is internal
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
     * @return string[]
     */
    public function getParameterIds()
    {
        return array_merge(
                [$this->evaluationContextFactory->getCallableId()],
                array_keys($this->evaluationContextFactory->getParameterScopedVariableMap())
        );
    }

    /**
     * Gets an evaluation context factory of the function.
     *
     * @return FunctionEvaluationContextFactory
     */
    public function getEvaluationContextFactory()
    {
        return $this->evaluationContextFactory;
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
        if ($this->evaluationContextFactory->getScopeType() === $scopeType
                && $this->evaluationContextFactory->getNamespace() === $namespace
                && $this->evaluationContextFactory->getParameterScopedVariableMap() === $parameterScopedVariableMap
                && $this->parameters->getAll() === $parameterExpressions
                && $this->bodyExpressions === $bodyExpressions
        ) {
            return $this;
        }

        return new static(
                $this->evaluationContextFactory->getCallableId(),
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
                $this->evaluationContextFactory->getScopeType(),
                $this->evaluationContextFactory->getNamespace(),
                $this->evaluationContextFactory->getParameterScopedVariableMap(),
                $this->parameters->getAll(),
                $bodyExpressions
        );
    }

    /**
     * @param O\ExpressionWalker $walker
     *
     * @return static
     */
    final public function walk(O\ExpressionWalker $walker)
    {
        return $this->update(
                $this->evaluationContextFactory->getScopeType(),
                $this->evaluationContextFactory->getNamespace(),
                $this->evaluationContextFactory->getParameterScopedVariableMap(),
                $walker->walkAll($this->parameters->getAll()),
                $this->isInternal() ? null : $walker->walkAll($this->bodyExpressions)
        );
    }
}
