<?php

namespace Pinq\Queries\Requests; 

class Maximum extends ProjectionRequest
{
    public function GetType()
    {
        return self::Maximum;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitMaximum($this);
    }
}
