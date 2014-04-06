<?php

namespace Pinq\Queries\Requests; 

class Values extends Request
{
    public function GetType()
    {
        return self::Values;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitValues($this);
    }
}
