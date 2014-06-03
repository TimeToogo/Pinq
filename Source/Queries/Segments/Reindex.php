<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for numerically reindexing the values by their
 * 0-based position
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Reindex extends Segment
{
    public function getType()
    {
        return self::REINDEX;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkReindex($this);
    }
}
