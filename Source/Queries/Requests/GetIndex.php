<?php

namespace Pinq\Queries\Requests; 

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
