<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for indexing the values by the supplied function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IndexBy extends ExpressionSegment
{
    public function getType()
    {
        return self::INDEX_BY;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkIndexBy($this);
    }
}
