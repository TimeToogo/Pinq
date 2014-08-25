<?php

namespace Pinq\Queries;

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
     * @return string[]
     */
    public function getParameters();

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

    /**
     * @param ISourceInfo $sourceInfo
     * @param ISegment[]  $segments
     *
     * @return IScope
     */
    public function update(ISourceInfo $sourceInfo, array $segments);

    /**
     * @param ISourceInfo $sourceInfo
     *
     * @return IScope
     */
    public function updateSource(ISourceInfo $sourceInfo);

    /**
     * @param ISegment[] $segments
     *
     * @return IScope
     */
    public function updateSegments(array $segments);
}
