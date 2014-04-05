<?php

namespace Pinq\Queries;

class Filter extends ExpressionQuery
{
    public function GetType()
    {
        return self::Filter;
    }

    public function Traverse(QueryStreamWalker $Walker)
    {
        return $Walker->WalkFilter($this);
    }
}
