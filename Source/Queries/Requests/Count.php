<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for an integer of the amount of values in the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Count extends Request
{
    public function getType()
    {
        return self::COUNT;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitCount($this);
    }
}
