<?php

namespace Pinq\Queries\Operations; 

class RemoveValues extends ValuesOperation
{
    public function GetType()
    {
        return self::RemoveValues;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitRemoveValues($this);
    }

}
