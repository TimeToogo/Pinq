<?php

namespace Pinq\Queries\Operations; 

/**
 * Operation query for removing values that satisfy the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RemoveWhere extends ExpressionOperation
{
    public function GetType()
    {
        return self::RemoveWhere;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitRemoveWhere($this);
    }

}
