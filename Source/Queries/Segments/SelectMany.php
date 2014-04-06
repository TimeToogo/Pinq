<?php

namespace Pinq\Queries\Segments;

class SelectMany extends ExpressionSegment
{
    public function GetType()
    {
        return self::SelectMany;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkSelectMany($this);
    }

}
