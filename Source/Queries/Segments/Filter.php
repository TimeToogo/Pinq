<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for filtering the values based on the supplied function
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Filter extends ProjectionSegment
{
    public function getType()
    {
        return self::FILTER;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitFilter($this);
    }
}
