<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether the supplied value
 * is contained within the scope
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Contains extends Request
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return self::CONTAINS;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitContains($this);
    }
}
