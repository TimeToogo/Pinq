<?php

namespace Pinq\Queries\Operations; 

class AddValues extends ValuesOperation
{
    public function GetType()
    {
        return self::AddValues;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitAddValues($this);
    }

}
