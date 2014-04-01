<?php

namespace Pinq\Queries\Expression;

class SelectMany extends ExpressionQuery
{
    public function GetType()
    {
        return self::SelectMany;
    }

    public function TraverseQuery(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitSelectMany($this);
    }

}
