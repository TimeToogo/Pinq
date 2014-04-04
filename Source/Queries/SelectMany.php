<?php

namespace Pinq\Queries;

class SelectMany extends ExpressionQuery
{
    public function GetType()
    {
        return self::SelectMany;
    }

    public function TraverseQuery(QueryStreamWalker $Walker)
    {
        return $Walker->VisitSelectMany($this);
    }

}
