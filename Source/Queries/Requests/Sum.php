<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a double of the sum of all the projected values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
