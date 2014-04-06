<?php

namespace Pinq\Queries\Segments; 

class Filter extends ExpressionSegment
{
    public function GetType()
    {
        return self::Filter;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkFilter($this);
    }
}
