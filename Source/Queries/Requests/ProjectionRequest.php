<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\Functions;

/**
 * Base class for a request which optionally projects the elements with
 * the supplied function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ProjectionRequest extends ProjectionRequestBase
{
    final public function __construct(Functions\ElementProjection $projectionFunction = null)
    {
        parent::__construct($projectionFunction);
    }

    /**
     * @param Functions\ElementProjection $projectionFunction
     *
     * @return static
     */
    public function update(Functions\ElementProjection $projectionFunction = null)
    {
        return $this->updateProjection($projectionFunction);
    }

    protected function withProjectionFunction(Functions\ElementProjection $projectionFunction = null)
    {
        return new static($projectionFunction);
    }
}
