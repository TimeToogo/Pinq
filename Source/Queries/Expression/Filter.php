<?php

namespace Pinq\Queries\Expression;

class Filter extends ExpressionQuery
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
