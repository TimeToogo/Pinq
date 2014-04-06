<?php

namespace Pinq\Queries\Requests; 

class Last extends Request
{
    public function GetType()
    {
        return self::Last;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitLast($this);
    }
}
