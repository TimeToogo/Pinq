<?php

namespace Pinq\Queries\Operations; 

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
