<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether all the values satisfy the
 * supplied predicate function or are truthy if no function is supplied.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class All extends ProjectionRequest
{
    public function getType()
    {
        return self::ALL;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitAll($this);
    }
}
