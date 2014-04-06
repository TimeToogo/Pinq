<?php

namespace Pinq\Queries\Requests; 

class First extends Request
{
    public function GetType()
    {
        return self::First;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitFirst($this);
    }
}
