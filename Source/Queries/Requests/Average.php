<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a double of the average of all the projected
 * values
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Average extends ProjectionRequest
{
    public function getType()
    {
        return self::AVERAGE;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitAverage($this);
    }
}
