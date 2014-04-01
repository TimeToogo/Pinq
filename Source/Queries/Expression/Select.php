<?php

namespace Pinq\Queries\Expression;

class Select extends ExpressionQuery
{
    public function GetType()
    {
        return self::Select;
    }

    public function TraverseQuery(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitSelect($this);
    }

}
