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

    public function getParameters()
    {
        if ($this->isEmpty()) {
            return [];
        }

        $parameterGroups = [];
        foreach ($this->segments as $segment) {
            $parameterGroups[] = $segment->getParameters();
        }

        return call_user_func_array('array_merge', $parameterGroups);
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
        foreach ($this->segments as $segment) {
            $segment->traverse($visitor);
        }
    }

    public function update(ISourceInfo $sourceInfo, array $segments)
    {
        if ($this->sourceInfo === $sourceInfo && $this->segments === $segments) {
            return $this;
        }

        return new self($sourceInfo, $segments);
    }

    public function updateSource(ISourceInfo $sourceInfo)
    {
        return $this->update($sourceInfo, $this->segments);
    }

    public function updateSegments(array $segments)
    {
        return $this->update($this->sourceInfo, $segments);
    }
}
