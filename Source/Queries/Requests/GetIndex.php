<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a value at the specified index
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GetIndex extends IndexRequest
{
    public function GetType()
    {
        return self::GetIndex;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitGetIndex($this);
    }

}
