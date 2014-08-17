<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether any of the values satisfy the
 * supplied predicate function or are truthy if no function is supplied.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Any extends ProjectionRequest
{
    public function getType()
    {
        return self::ANY;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitAny($this);
    }
}
