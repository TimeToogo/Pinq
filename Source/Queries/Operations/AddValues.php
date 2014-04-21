<?php

namespace Pinq\Queries\Operations; 

/**
 * Operation query for adding a range of values to the source
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
