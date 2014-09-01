<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Interface containing the scope data of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionScope
{
    /**
     * Whether the function has a scoped $this class.
     *
     * @return boolean
     */
    public function hasThis();

    /**
     * Gets the scoped $this variable type.
     *
     * @return string|null
     */
    public function getThisType();

    /**
     * Gets the scoped $this variable object.
     *
     * @return object|null
     */
    public function getThis();

    /**
     * Gets the scoped class type (determines which private/protected members are accessible).
     *
     * @return string|null
     */
    public function getScopeType();

    /**
     * Gets an array containing scoped values indexed by their
     * respective variable name.
     *
     * @return array<string, mixed>
     */
    public function getVariableTable();

    /**
     * Gets an equivalent evaluation context for the function scope.
     *
     * @param mixed       $variableTable
     * @param string|null $namespace
     *
     * @return O\IEvaluationContext
     */
    public function asEvaluationContext(array $variableTable = [], $namespace = null);
}
