<?php

namespace Pinq\Queries\Functional;

class IndexBy extends FunctionQuery
{
    public function GetType()
    {
        return self::IndexBy;
    }

    public function TraverseQuery(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitIndexBy($this);
    }

}
