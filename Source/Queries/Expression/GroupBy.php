<?php

namespace Pinq\Queries\Expression;

class GroupBy extends ExpressionQuery
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
