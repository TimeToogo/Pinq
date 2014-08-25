<?php

namespace Pinq\Queries;

/**
 * An operation query is a type of query for IRepository, it represents
 * an action to execute against the supplied scope of the source values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationQuery extends IQuery
{
    /**
     * @return IOperation
     */
    public function getOperation();

    /**
     * Returns the query with the supplied scope and operation.
     *
     * @param IScope     $scope
     * @param IOperation $operation
     *
     * @return IOperationQuery
     */
    public function update(IScope $scope, IOperation $operation);

    /**
     * Returns the query with the supplied operation.
     *
     * @param IOperation $operation
     *
     * @return IOperationQuery
     */
    public function updateOperation(IOperation $operation);
}
