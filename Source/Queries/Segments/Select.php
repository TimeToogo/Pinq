<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the values mapped by the supplied function
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Select extends ProjectionSegment
{
    public function getType()
    {
        return self::SELECT;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitSelect($this);
    }
}
