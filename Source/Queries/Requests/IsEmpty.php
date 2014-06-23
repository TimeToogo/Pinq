<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether any values are
 * contained in the scope
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IsEmpty extends Request
{
    public function getType()
    {
        return self::IS_EMPTY;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitIsEmpty($this);
    }
}
