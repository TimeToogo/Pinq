<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for an iterator which will iterate all the values
 * of the current scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Values extends Request
{
    public function GetType()
    {
        return self::Values;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitValues($this);
    }
}
