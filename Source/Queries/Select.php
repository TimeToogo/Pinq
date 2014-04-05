<?php

namespace Pinq\Queries;

class Select extends ExpressionQuery
{
    public function GetType()
    {
        return self::Select;
    }

    public function Traverse(QueryStreamWalker $Walker)
    {
        return $Walker->WalkSelect($this);
    }

}
