<?php

namespace Pinq\Queries\Requests; 

class Sum extends ProjectionRequest
{
    public function GetType()
    {
        return self::Sum;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitSum($this);
    }
}
