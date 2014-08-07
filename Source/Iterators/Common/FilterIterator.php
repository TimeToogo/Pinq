<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the filter iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait FilterIterator
{
    /**
     * @var callable
     */
    protected $filter;

    final protected function __constructIterator(callable $filter)
    {
        $this->filter = Functions::allowExcessiveArguments($filter);
    }
}
