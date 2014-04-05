<?php

namespace Pinq\Queries;

class Range implements IQuery
{
    private $RangeStart = 0;
    private $RangeAmount = 0;

    public function __construct($RangeStart, $RangeAmount)
    {
        $this->RangeStart = $RangeStart;
        $this->RangeAmount = $RangeAmount;
    }

    public function GetType()
    {
        return self::Range;
    }

    public function Traverse(QueryStreamWalker $Walker)
    {
        return $Walker->WalkRange($this);
    }

    /**
     * @return int
     */
    public function GetRangeStart()
    {
        return $this->RangeStart;
    }

    /**
     * @return int|null
     */
    public function GetRangeAmount()
    {
        return $this->RangeAmount;
    }
    
    public function Update($RangeStart, $RangeAmount)
    {
        if($this->RangeStart === $RangeStart
                && $this->RangeAmount === $RangeAmount) {
            return $this;
        }
        
        return new self($RangeStart, $RangeAmount);
    }
}
