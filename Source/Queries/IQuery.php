<?php

namespace Pinq\Queries;

/**
 * Base interface for request and operation queries.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQuery
{
    /**
     * Gets the query parameter registry.
     *
     * @return IParameterRegistry
     */
    public function getParameters();

    /**
     * Gets the query scope.
     *
     * @return IScope
     */
    public function getScope();

    /**
     * Returns the query with the supplied scope.
     *
     * @param IScope $scope
     *
     * @return static
     */
    public function updateScope(IScope $scope);
}
