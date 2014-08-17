<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for numerically reindexing the values by their
 * 0-based position
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Reindex extends Segment
{
    public function getType()
    {
        return self::REINDEX;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitReindex($this);
    }
}
