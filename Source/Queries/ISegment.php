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
    const FILTER = 0;
    const ORDER_BY = 1;
    const RANGE = 2;
    const GROUP_BY = 3;
    const JOIN = 4;
    const EQUALITY_JOIN = 5;
    const SELECT = 6;
    const SELECT_MANY = 7;
    const OPERATE = 8;
    const UNIQUE = 9;
    const INDEX_BY = 10;

    /**
     * @return int The query type
     */
    public function getType();

    /**
     * @return ISegment
     */
    public function traverse(Segments\SegmentWalker $walker);
}
