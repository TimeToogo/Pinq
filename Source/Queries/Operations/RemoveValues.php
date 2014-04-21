<?php

namespace Pinq\Queries\Operations; 

/**
 * Operation query for removing a range of values to the source
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
