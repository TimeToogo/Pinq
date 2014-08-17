<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for the minimum projected value in the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Minimum extends ProjectionRequest
{
    public function getType()
    {
        return self::MINIMUM;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitMinimum($this);
    }
}
