<?php

namespace Pinq\Queries\Functional;

class Select extends FunctionQuery
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
