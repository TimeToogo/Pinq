<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for selecting the keys as the values,
 * indexed by their 0-based position
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Keys extends Segment
{
    public function getType()
    {
        return self::KEYS;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitKeys($this);
    }
}
