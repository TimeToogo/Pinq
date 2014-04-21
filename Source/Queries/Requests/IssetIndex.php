<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a boolean of whether a specified index is set
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IssetIndex extends IndexRequest
{
    public function GetType()
    {
        return self::IssetIndex;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitIssetIndex($this);
    }

}
