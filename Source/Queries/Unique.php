<?php

namespace Pinq\Queries;

class Unique implements IQuery
{
    public function GetType()
    {
        return self::Unique;
    }

    public function Traverse(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitUnique($this);
    }

}
