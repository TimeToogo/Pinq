<?php

namespace Pinq\Queries\Operations; 

/**
 * Operation query for clearing all values from the source
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Clear extends Operation
{
    public function GetType()
    {
        return self::Clear;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitClear($this);
    }

}
