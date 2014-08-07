<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a the first value in the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class First extends Request
{
    public function getType()
    {
        return self::FIRST;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitFirst($this);
    }
}
