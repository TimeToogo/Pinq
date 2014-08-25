<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the specified range of values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Range extends Segment
{
    /**
     * @var string
     */
    private $startParameterId;

    /**
     * @var string
     */
    private $amountParameterId;

    public function __construct($startParameterId, $amountParameterId)
    {
        $this->startParameterId  = $startParameterId;
        $this->amountParameterId = $amountParameterId;
    }

    public function getType()
    {
        return self::RANGE;
    }

    public function getParameters()
    {
        return [$this->startParameterId, $this->amountParameterId];
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitRange($this);
    }

    /**
     * Gets the start parameter id.
     *
     * @return string
     */
    public function getStartId()
    {
        return $this->startParameterId;
    }

    /**
     * Gets the amount parameter id.
     *
     * @return string
     */
    public function getAmountId()
    {
        return $this->amountParameterId;
    }
}
