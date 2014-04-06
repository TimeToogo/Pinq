<?php

namespace Pinq\Queries\Requests; 

class All extends ProjectionRequest
{
    public function GetType()
    {
        return self::All;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitAll($this);
    }
}
