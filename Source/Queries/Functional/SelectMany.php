<?php

namespace Pinq\Queries\Functional;

class SelectMany extends FunctionQuery
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
