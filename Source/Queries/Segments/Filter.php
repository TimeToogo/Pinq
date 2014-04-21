<?php

namespace Pinq\Queries\Segments; 

/**
 * Query segment for filtering the values based on the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
