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
}
