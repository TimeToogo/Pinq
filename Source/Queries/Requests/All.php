<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether all the values satify the
 * supplied predicate function
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class All extends ProjectionRequest
{
    public function getType()
    {
        return self::ALL;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitAll($this);
    }
}
