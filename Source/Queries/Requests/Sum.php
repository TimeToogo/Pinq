<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a double of the sum of all the projected values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Sum extends ProjectionRequest
{
    public function getType()
    {
        return self::SUM;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitSum($this);
    }
}
