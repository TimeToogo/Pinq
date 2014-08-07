<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\Functions;

/**
 * Base class for a request which optionally projects the elements with
 * the supplied function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ProjectionRequest extends Request
{
    /**
     * @var Functions\ElementProjection|null
     */
    private $projectionFunction;

    public function __construct(Functions\ElementProjection $projectionFunction = null)
    {
        $this->projectionFunction = $projectionFunction;
    }

    /**
     * @return boolean
     */
    public function hasProjectionFunction()
    {
        return $this->projectionFunction !== null;
    }

    /**
     * @return Functions\ElementProjection|null
     */
    public function getProjectionFunction()
    {
        return $this->projectionFunction;
    }
}
