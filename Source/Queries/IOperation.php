<?php

namespace Pinq\Queries;

/**
 * The interface for a operation query, one of the const types, they
 * are all implemented as their own class in Operations namespace.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IOperation
{
    const Apply = 0;
    const RemoveValues = 1;
    const AddValues = 2;
    const Clear = 3;
    const RemoveWhere = 4;
    const SetIndex = 7;
    const UnsetIndex = 8;

    /**
     * @return int
     */
    public function GetType();

    /**
     * @return void
     */
    public function Traverse(Operations\OperationVisitor $Visitor);
}
