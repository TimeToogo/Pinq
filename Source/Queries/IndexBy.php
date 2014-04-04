<?php

namespace Pinq\Queries;

class IndexBy extends ExpressionQuery
{
    public function GetType()
    {
        return self::IndexBy;
    }

    public function TraverseQuery(QueryStreamWalker $Walker)
    {
        return $Walker->VisitIndexBy($this);
    }

}
