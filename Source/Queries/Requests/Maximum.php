<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for the maximum projected value in the scope
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Maximum extends ProjectionRequest
{
    public function getType()
    {
        return self::MAXIMUM;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitMaximum($this);
    }
}
