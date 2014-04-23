<?php 

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the values mapped by the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Select extends ExpressionSegment
{
    public function getType()
    {
        return self::SELECT;
    }
    
    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkSelect($this);
    }
}