<?php

namespace Pinq\Queries;

class SelectMany extends ExpressionQuery
{
    public function GetType()
    {
        return self::SelectMany;
    }

    public function Traverse(QueryStreamWalker $Walker)
    {
        return $Walker->WalkSelectMany($this);
    }

}
