<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for selecting the keys as the values,
 * indexed by their 0-based position
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Keys extends Segment
{
    public function getType()
    {
        return self::KEYS;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkKeys($this);
    }
}
