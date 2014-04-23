<?php

namespace Pinq\Queries;

/**
 * Implementation of the IScope
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Scope implements IScope
{
    /**
     * @var ISegment[]
     */
    private $segments = [];

    public function __construct(array $segments)
    {
        $this->segments = $segments;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->segments);
    }

    /**
     * @return ISegment[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    public function isEmpty()
    {
        return empty($this->segments);
    }

    public function append(ISegment $query)
    {
        return new self(array_merge($this->segments, [$query]));
    }

    public function update(array $segments)
    {
        if ($this->segments === $segments) {
            return $this;
        }

        return new self($segments);
    }

    public function updateLast(ISegment $segment)
    {
        if (end($this->segments) === $segment) {
            return $this;
        }

        return new self(array_merge(array_slice($this->segments, 0, -1), [$segment]));
    }
}
