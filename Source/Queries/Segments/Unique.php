<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving only unique values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Unique extends Segment
{
    public function getType()
    {
        return self::UNIQUE;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitUnique($this);
    }
}
