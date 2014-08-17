<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\Functions;

/**
 * Request query for a string of all the projected values
 * concatenated by the specified delimiter
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Implode extends ProjectionRequest
{
    /**
     * @var string
     */
    private $delimiterId;

    public function __construct($delimiterId, Functions\ElementProjection $projectionFunction = null)
    {
        parent::__construct($projectionFunction);

        $this->delimiterId = $delimiterId;
    }

    public function getType()
    {
        return self::IMPLODE;
    }

    /**
     * Gets the value parameter id.
     *
     * @return string
     */
    public function getDelimiterId()
    {
        return $this->delimiterId;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitImplode($this);
    }
}
