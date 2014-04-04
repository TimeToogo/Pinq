<?php

namespace Pinq\Queries;

class Select extends ExpressionQuery
{
    public function GetType()
    {
        return self::Select;
    }

    public function TraverseQuery(QueryStreamWalker $Walker)
    {
        return $Walker->VisitSelect($this);
    }

}
