<?php

namespace Pinq\Queries;

use Pinq\Queries\Segments\ISegmentVisitor;

/**
 * Implementation of the IScope
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Scope implements IScope
{
    /**
     * @var ISourceInfo
     */
    protected $sourceInfo;

    /**
     * @var ISegment[]
     */
    protected $segments = [];

    public function __construct(ISourceInfo $sourceInfo, array $segments)
    {
        $this->sourceInfo = $sourceInfo;
        $this->segments   = $segments;
    }

    public function getSourceInfo()
    {
        return $this->sourceInfo;
    }

    public function getSegments()
    {
        return $this->segments;
    }

    public function isEmpty()
    {
        return empty($this->segments);
    }

    public function visit(ISegmentVisitor $visitor)
    {
        foreach($this->segments as $segment) {
            $segment->traverse($visitor);
        }
    }
}
