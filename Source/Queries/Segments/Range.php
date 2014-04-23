<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving the specified range of values
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Range extends Segment
{
    private $rangeStart = 0;

    private $rangeAmount = 0;

    public function __construct($rangeStart, $rangeAmount)
    {
        $this->rangeStart = $rangeStart;
        $this->rangeAmount = $rangeAmount;
    }

    public function getType()
    {
        return self::RANGE;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkRange($this);
    }

    /**
     * @return int
     */
    public function getRangeStart()
    {
        return $this->rangeStart;
    }

    /**
     * @return int|null
     */
    public function getRangeAmount()
    {
        return $this->rangeAmount;
    }

    public function update($rangeStart, $rangeAmount)
    {
        if ($this->rangeStart === $rangeStart && $this->rangeAmount === $rangeAmount) {
            return $this;
        }

        return new self($rangeStart, $rangeAmount);
    }
}
