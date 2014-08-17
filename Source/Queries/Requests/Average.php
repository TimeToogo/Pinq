<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a double of the average of all the projected
 * values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Average extends ProjectionRequest
{
    public function getType()
    {
        return self::AVERAGE;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitAverage($this);
    }
}
