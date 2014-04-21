<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a the first value in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class First extends Request
{
    public function GetType()
    {
        return self::First;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitFirst($this);
    }
}
