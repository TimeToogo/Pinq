<?php
namespace Pinq\Expressions;

/**
 * Interface of the evaluation context.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IEvaluationContext
{
    /**
     * Whether the evaluation is performed under a namespace.
     *
     * @return boolean
     */
    public function hasNamespace();

    /**
     * Gets the namespace to perform the evaluation.
     *
     * @return string|null
     */
    public function getNamespace();

    /**
     * Whether the evaluation is performed in a class scope.
     *
     * @return boolean
     */
    public function hasScopeType();

    /**
     * Gets the scope type to perform the evaluation.
     *
     * @return string|null
     */
    public function getScopeType();

    /**
     * Whether there is an instance bound to the $this variable.
     *
     * @return boolean
     */
    public function hasThis();

    /**
     * Gets the instance bound to $this variable.
     *
     * @return object|null
     */
    public function getThis();

    /**
     * Gets a map of values indexed by their respective variable name.
     *
     * @return array<string, mixed>
     */
    public function getVariableTable();

    /**
     * Returns a new evaluation context with the variable value map.
     *
     * @param array<string, mixed> $variableValueMap
     *
     * @return IEvaluationContext
     */
    public function withVariableTable(array $variableTable);
}
