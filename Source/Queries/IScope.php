<?php

namespace Pinq\Queries;

/**
 * The query scope. This contains many query segments which
 * in order represent the scope of the query to be loaded/executed.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScope
{
    /**
     * @return ISourceInfo
     */
    public function getSourceInfo();

    /**
     * @return ISegment[]
     */
    public function getSegments();

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @param Segments\SegmentVisitor $visitor
     *
     * @return void
     */
    public function visit(Segments\SegmentVisitor $visitor);
}
