<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the values mapped by the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
