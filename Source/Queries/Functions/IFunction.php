<?php
namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\PinqException;

/**
 * Interface of a function structure.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunction extends \Serializable
{
    /**
     * Gets the parameter id of the callable for the function.
     *
     * @return string
     */
    public function getCallableId();

    /**
     * Whether the function has a scoped type.
     *
     * @return boolean
     */
    public function hasScopeType();

    /**
     * Gets the bound type of the function.
     * Null if there is no bound type.
     *
     * @return string|null
     */
    public function getScopeType();

    /**
     * Whether the function is defined in a namespace.
     *
     * @return boolean
     */
    public function hasNamespace();

    /**
     * Gets the namespace the function was defined in.
     * Null if was defined in the global namespace.
     *
     * @return string|null
     */
    public function getNamespace();

    /**
     * @return boolean
     */
    public function isInternal();

    /**
     * Gets an array containing the parameter ids as keys with their
     * respective scoped variable name as the value.
     *
     * @return array<string, string>
     */
    public function getParameterScopedVariableMap();

    /**
     * Gets the body expressions of the function.
     *
     * @return O\Expression[]
     * @throws PinqException  if the function is internal
     */
    public function getBodyExpressions();

    /**
     * Gets the body expressions of the function before and including
     * the first return statement.
     *
     * @return O\Expression[]
     * @throws PinqException  if the function is internal
     */
    public function getBodyExpressionsUntilReturn();

    /**
     * Gets amount of body expressions of the function.
     *
     * @return int
     * @throws PinqException if the function is internal
     */
    public function countBodyExpressions();

    /**
     * Gets amount of body expressions of the function before and including
     * the first return statement.
     *
     * @return int
     * @throws PinqException if the function is internal
     */
    public function countBodyExpressionsUntilReturn();

    /**
     * @return Parameters\ParameterBase
     */
    public function getParameters();

    /**
     * @return string[]
     */
    public function getParameterIds();

    /**
     * Gets an evaluation context factory of the function.
     *
     * @return FunctionEvaluationContextFactory
     */
    public function getEvaluationContextFactory();

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
    );

    /**
     * @param O\Expression[]|null $bodyExpressions
     *
     * @return static
     */
    public function updateBody(array $bodyExpressions = null);

    /**
     * @param O\ExpressionWalker $walker
     *
     * @return static
     */
    public function walk(O\ExpressionWalker $walker);
}