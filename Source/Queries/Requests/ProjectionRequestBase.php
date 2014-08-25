<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\Functions;

/**
 * Base class for a request which optionally projects the elements with
 * the supplied function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ProjectionRequestBase extends Request
{
    /**
     * @var Functions\ElementProjection|null
     */
    protected $projectionFunction;

    public function __construct(Functions\ElementProjection $projectionFunction = null)
    {
        $this->projectionFunction = $projectionFunction;
    }

    public function getParameters()
    {
        return $this->projectionFunction === null ? [] : $this->projectionFunction->getParameterIds();
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

    /**
     * @param Functions\ElementProjection $projectionFunction
     *
     * @return static
     */
    public function updateProjection(Functions\ElementProjection $projectionFunction = null)
    {
        if ($this->projectionFunction === $projectionFunction) {
            return $this;
        }

        return $this->withProjectionFunction($projectionFunction);
    }

    abstract protected function withProjectionFunction(Functions\ElementProjection $projectionFunction = null);
}
