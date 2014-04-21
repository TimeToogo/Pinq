<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for the maximum projected value in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
