<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for the minimum projected value in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
