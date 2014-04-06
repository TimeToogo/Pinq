<?php

namespace Pinq\Queries\Requests; 

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
