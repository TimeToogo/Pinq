<?php

namespace Pinq\Queries;

class IndexBy extends ExpressionQuery
{
    public function GetType()
    {
        return self::IndexBy;
    }

    public function Traverse(QueryStreamWalker $Walker)
    {
        return $Walker->WalkIndexBy($this);
    }

}
