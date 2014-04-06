<?php

namespace Pinq\Queries\Requests; 

class Minimum extends ProjectionRequest
{
    public function GetType()
    {
        return self::Minimum;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitMinimum($this);
    }
}
