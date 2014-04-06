<?php

namespace Pinq\Queries\Segments;

class IndexBy extends ExpressionSegment
{
    public function GetType()
    {
        return self::IndexBy;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkIndexBy($this);
    }

}
