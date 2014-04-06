<?php

namespace Pinq\Queries\Requests; 

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
