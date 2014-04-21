<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for indexing the values by the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
