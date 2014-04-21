<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the values mapped by the supplied function
 * which will be flattened into single range of values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
