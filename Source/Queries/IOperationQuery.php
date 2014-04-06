<?php

namespace Pinq\Queries;

interface IOperationQuery extends IQuery
{
    /**
     * @return IOperation
     */
    public function GetOperation();
}
