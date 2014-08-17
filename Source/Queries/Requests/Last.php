<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for the last value in the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Last extends Request
{
    public function getType()
    {
        return self::LAST;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitLast($this);
    }
}
