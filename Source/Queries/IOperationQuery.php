<?php

namespace Pinq\Queries;

/**
 * An operation query is a type of query for IRepository, it represents
 * an action to execute against the supplied scope of the source values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IOperationQuery extends IQuery
{
    /**
     * @return IOperation
     */
    public function getOperation();
}
