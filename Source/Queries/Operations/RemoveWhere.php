<?php

namespace Pinq\Queries\Operations; 

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
