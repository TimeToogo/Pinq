<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for the maximum projected value in the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Maximum extends ProjectionRequest
{
    public function getType()
    {
        return self::MAXIMUM;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitMaximum($this);
    }
}
