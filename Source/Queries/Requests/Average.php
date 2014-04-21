<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a double of the average of all the projected
 * values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
