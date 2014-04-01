<?php

namespace Pinq\Queries\Functional;

class Filter extends FunctionQuery
{
    public function GetType()
    {
        return self::Filter;
    }

    public function TraverseQuery(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitFilter($this);
    }

}
