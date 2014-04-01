<?php

namespace Pinq\Queries\Functional;

class GroupBy extends FunctionQuery
{
    public function GetType()
    {
        return self::GroupBy;
    }

    public function TraverseQuery(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitGroupBy($this);
    }

}
