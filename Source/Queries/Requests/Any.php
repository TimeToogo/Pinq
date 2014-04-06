<?php

namespace Pinq\Queries\Requests; 

class Any extends ProjectionRequest
{
    public function GetType()
    {
        return self::Any;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitAny($this);
    }
}
