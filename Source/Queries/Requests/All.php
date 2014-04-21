<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a boolean of whether all the values satify the 
 * supplied predicate function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
