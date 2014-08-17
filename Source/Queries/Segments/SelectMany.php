<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the values mapped by the supplied function
 * which will be flattened into single range of values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SelectMany extends ProjectionSegment
{
    public function getType()
    {
        return self::SELECT_MANY;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitSelectMany($this);
    }
}
