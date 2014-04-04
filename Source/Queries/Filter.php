<?php

namespace Pinq\Queries;

class Filter extends ExpressionQuery
{
    public function GetType()
    {
        return self::Filter;
    }

    public function TraverseQuery(QueryStreamWalker $Walker)
    {
        return $Walker->VisitFilter($this);
    }
}
