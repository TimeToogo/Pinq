<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\Functions;

/**
 * Base class for a query segment with a projection function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ProjectionSegment extends Segment
{
    /**
     * @var Functions\ElementProjection
     */
    private $projectionFunction;

    final public function __construct(Functions\ElementProjection $projectionFunction)
    {
        $this->projectionFunction = $projectionFunction;
    }

    /**
     * @return Functions\ElementProjection
     */
    public function getProjectionFunction()
    {
        return $this->projectionFunction;
    }

    public function update(Functions\ElementProjection $projectionFunction)
    {
        if ($this->projectionFunction === $projectionFunction) {
            return $this;
        }

        return new static($projectionFunction);
    }
}
