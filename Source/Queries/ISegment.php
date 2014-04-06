<?php

namespace Pinq\Queries; 

interface ISegment
{
    const Filter = 0;
    const OrderBy = 1;
    const Range = 2;
    const GroupBy = 3;
    const Select = 4;
    const SelectMany = 5;
    const Operate = 6;
    const Unique = 7;
    const IndexBy = 8;

    /**
     * @return int The query type
     */
    public function GetType();

    /**
     * @return ISegment
     */
    public function Traverse(Segments\SegmentWalker $Walker);
}
