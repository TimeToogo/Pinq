<?php

namespace Pinq\Queries; 

interface ISegment
{
    const Filter = 0;
    const OrderBy = 1;
    const Range = 2;
    const GroupBy = 3;
    const Join = 4;
    const EqualityJoin = 5;
    const Select = 6;
    const SelectMany = 7;
    const Operate = 8;
    const Unique = 9;
    const IndexBy = 10;

    /**
     * @return int The query type
     */
    public function GetType();

    /**
     * @return ISegment
     */
    public function Traverse(Segments\SegmentWalker $Walker);
}
