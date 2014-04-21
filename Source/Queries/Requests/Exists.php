<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a boolean of whether any values are 
 * contained in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Exists extends Request
{
    public function GetType()
    {
        return self::Exists;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitExists($this);
    }
}
