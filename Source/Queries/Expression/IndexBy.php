<?php

namespace Pinq\Queries\Expression;

class IndexBy extends ExpressionQuery
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
