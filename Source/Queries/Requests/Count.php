<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for an integer of the amount of values in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Count extends Request
{
    public function GetType()
    {
        return self::Count;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitCount($this);
    }
}
