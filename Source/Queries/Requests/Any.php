<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a boolean of whether any of the values satify the 
 * supplied predicate function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
