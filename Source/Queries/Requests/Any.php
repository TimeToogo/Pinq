<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether any of the values satify the
 * supplied predicate function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Any extends ProjectionRequest
{
    public function getType()
    {
        return self::ANY;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitAny($this);
    }
}
