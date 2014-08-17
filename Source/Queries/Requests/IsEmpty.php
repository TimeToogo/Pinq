<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether any values are
 * contained in the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IsEmpty extends Request
{
    public function getType()
    {
        return self::IS_EMPTY;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitIsEmpty($this);
    }
}
