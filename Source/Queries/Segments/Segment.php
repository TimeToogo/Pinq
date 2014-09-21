<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\ISegment;

/**
 * Base class for a query segment
 * Currently here for convenient namespacing
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Segment implements ISegment
{
    public function getParameters()
    {
        return [];
    }
}
