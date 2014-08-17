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

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitGetIndex($this);
    }
}
