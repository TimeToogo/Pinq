<?php

namespace Pinq\Queries\Requests; 

class Average extends ProjectionRequest
{
    public function GetType()
    {
        return self::Average;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitAverage($this);
    }
}
