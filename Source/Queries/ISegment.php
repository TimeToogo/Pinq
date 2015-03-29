<?php

namespace Pinq\Queries;

use Pinq\Queries\Segments\ISegmentVisitor;

/**
 * The interface for a query segment, one of the const types, they
 * are all implemented as their own class class in Segments namespace.
 * What separates a segment from a request is that a segment will always
 * return a projection of the original values, where as a request will not
 * remain queryable and thus is the final part of the query in IRequestQuery.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISegment
{
    const FILTER      = 0;
    const ORDER_BY    = 1;
    const RANGE       = 2;
    const GROUP_BY    = 3;
    const JOIN        = 4;
    const SELECT      = 5;
    const SELECT_MANY = 6;
    const OPERATION   = 7;
    const UNIQUE      = 8;
    const INDEX_BY    = 9;
    const KEYS        = 10;
    const REINDEX     = 11;

    /**
     * @return int The query type
     */
    public function getType();

    /**
     * @return string[]
     */
    public function getParameters();

    /**
     * @param Segments\ISegmentVisitor $visitor
     *
     * @return mixed
     */
    public function traverse(ISegmentVisitor $visitor);
}
