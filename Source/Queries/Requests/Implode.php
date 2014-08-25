<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\Functions;

/**
 * Request query for a string of all the projected values
 * concatenated with the specified delimiter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Implode extends ProjectionRequestBase
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

    public function getParameters()
    {
        return array_merge([$this->delimiterId], parent::getParameters());
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

    protected function withProjectionFunction(Functions\ElementProjection $projectionFunction = null)
    {
        return new self($this->delimiterId, $projectionFunction);
    }
}
