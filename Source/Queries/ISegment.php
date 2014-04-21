<?php

namespace Pinq\Queries; 

/**
 * The interface for a query segment, one of the const types, they
 * are all implemented as their own class class in Segments namespace.
 * 
 * What seperates a segment from a request is that a segment will always
 * return a projection of the original values, where as a request will not
 * remain queryable and thus is the final part of the query in IRequestQuery.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
