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

    public function Traverse(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitRange($this);
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
}
