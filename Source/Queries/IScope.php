<?php

namespace Pinq\Queries;

use Pinq\Queries\Segments\ISegmentVisitor;
use Pinq\Queries\Segments\ISegmentVisitor;

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
     * @param ISegmentVisitor $visitor
     *
     * @return void
     */
    public function visit(ISegmentVisitor $visitor);
}
