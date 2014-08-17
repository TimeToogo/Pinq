<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for grouping the values base on the supplied
 * grouping function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GroupBy extends ProjectionSegment
{
    public function getType()
    {
        return self::GROUP_BY;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitGroupBy($this);
    }
}
