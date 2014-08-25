<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether the supplied value
 * is contained within the scope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Contains extends Request
{
    /**
     * @var string
     */
    private $valueId;

    public function __construct($valueId)
    {
        $this->valueId = $valueId;
    }

    public function getType()
    {
        return self::CONTAINS;
    }

    public function getParameters()
    {
        return [$this->valueId];
    }

    /**
     * Gets the value parameter id.
     *
     * @return string
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitContains($this);
    }
}
