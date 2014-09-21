<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\Functions;

/**
 * Class for a ordering of element projections by a direction.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Ordering
{
    /**
     * @var Functions\ElementProjection
     */
    private $projectionFunction;

    /**
     * @var string
     */
    private $isAscendingId;

    public function __construct(Functions\ElementProjection $projectionFunction, $isAscendingId)
    {
        $this->projectionFunction = $projectionFunction;
        $this->isAscendingId      = $isAscendingId;
    }

    /**
     * @return string[]
     */
    public function getParameters()
    {
        return array_merge([$this->isAscendingId], $this->projectionFunction->getParameterIds());
    }

    /**
     * @return Functions\ElementProjection
     */
    public function getProjectionFunction()
    {
        return $this->projectionFunction;
    }

    /**
     * Gets the ascending flag parameter id.
     *
     * @return string
     */
    public function getIsAscendingId()
    {
        return $this->isAscendingId;
    }

    /**
     * @param Functions\ElementProjection $projectionFunction
     *
     * @return Ordering
     */
    public function update(Functions\ElementProjection $projectionFunction)
    {
        if ($this->projectionFunction === $projectionFunction) {
            return $this;
        }

        return new self($projectionFunction, $this->isAscendingId);
    }
}
