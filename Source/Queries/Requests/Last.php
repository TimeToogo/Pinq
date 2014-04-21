<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for the last value in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Last extends Request
{
    public function GetType()
    {
        return self::Last;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitLast($this);
    }
}
