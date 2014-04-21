<?php

namespace Pinq\Queries\Operations; 

/**
 * Operation query for unsetting a value at the specified index
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
