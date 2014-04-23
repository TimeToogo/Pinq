<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for filtering the values based on the supplied function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Filter extends ExpressionSegment
{
    public function getType()
    {
        return self::FILTER;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkFilter($this);
    }
}
