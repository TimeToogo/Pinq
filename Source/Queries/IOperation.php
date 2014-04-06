<?php

namespace Pinq\Queries;

interface IOperation
{
    const Apply = 0;
    const RemoveValues = 1;
    const AddValues = 2;
    const Clear = 3;
    const RemoveWhere = 4;

    /**
     * @return int
     */
    public function GetType();

    /**
     * @return void
     */
    public function Traverse(Operations\OperationVisitor $Visitor);
}
