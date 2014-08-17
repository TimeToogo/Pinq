<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for indexing the values by the supplied function
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IndexBy extends ProjectionSegment
{
    public function getType()
    {
        return self::INDEX_BY;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitIndexBy($this);
    }
}
