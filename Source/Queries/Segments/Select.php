<?php

namespace Pinq\Queries\Segments;

class Select extends ExpressionSegment
{
    public function GetType()
    {
        return self::Select;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkSelect($this);
    }

}
