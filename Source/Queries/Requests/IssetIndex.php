<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether a specified index is set
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IssetIndex extends IndexRequest
{
    public function getType()
    {
        return self::ISSET_INDEX;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitIssetIndex($this);
    }
}
