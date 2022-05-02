<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\PinqException;

/**
 * Base class of a function structure.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class FunctionBase implements IFunction
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

    final public function getCallableId()
    {
        return $this->evaluationContextFactory->getCallableId();
    }

    public function hasScopeType()
    {
        return $this->evaluationContextFactory->hasScopeType();
    }

    public function getScopeType()
    {
        return $this->evaluationContextFactory->getScopeType();
    }

    public function hasNamespace()
    {
        return $this->evaluationContextFactory->hasNamespace();
    }

    public function getNamespace()
    {
        return $this->evaluationContextFactory->getNamespace();
    }

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

    final public function __serialize(): array
    {
        return [
                $this->evaluationContextFactory,
                $this->parameters,
                $this->bodyExpressions,
                $this->dataToSerialize()
        ];
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

    final public function __unserialize(array $data): void
    {
        list(
                $this->evaluationContextFactory,
                $this->parameters,
                $this->bodyExpressions,
                $data) = $data;
        $this->unserializeData($data);
    }

    protected function unserializeData($data)
    {

    }

    final public function getBodyExpressions()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return $this->bodyExpressions;
    }

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

    final public function countBodyExpressions()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return count($this->bodyExpressions);
    }

    final public function countBodyExpressionsUntilReturn()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return count($this->getBodyExpressionsUntilReturn());
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameterIds()
    {
        return array_merge(
                [$this->evaluationContextFactory->getCallableId()],
                array_keys($this->evaluationContextFactory->getParameterScopedVariableMap())
        );
    }

    public function getEvaluationContextFactory()
    {
        return $this->evaluationContextFactory;
    }

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
