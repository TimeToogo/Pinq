<?php

namespace Pinq\Queries\Operations; 

class UnsetIndex extends IndexOperation
{
    public function GetType()
    {
        return self::UnsetIndex;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitUnsetIndex($this);
    }

}
