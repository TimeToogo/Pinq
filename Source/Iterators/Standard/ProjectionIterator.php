<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the projection iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ProjectionIterator extends IteratorIterator
{
    use Common\ProjectionIterator;

    public function __construct(
            IIterator $iterator,
            callable $keyProjectionFunction = null,
            callable $valueProjectionFunction = null
    ) {
        parent::__construct($iterator);
        self::__constructIterator($keyProjectionFunction, $valueProjectionFunction);
    }

    protected function doFetch()
    {
        if ($element = $this->iterator->fetch()) {
            return $this->projectElement($element[0], $element[1]);
        }
    }
}
