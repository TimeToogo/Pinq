<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a value at the specified index
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GetIndex extends IndexRequest
{
    public function getType()
    {
        return self::GET_INDEX;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitGetIndex($this);
    }
}
