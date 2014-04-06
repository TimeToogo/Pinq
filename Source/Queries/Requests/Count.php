<?php

namespace Pinq\Queries\Requests; 

class Count extends Request
{
    public function GetType()
    {
        return self::Count;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitCount($this);
    }
}
