<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the grouped iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait GroupedIterator
{
    /**
     * @var callable
     */
    protected $groupKeyFunction;

    /**
     * @var callable
     */
    protected $traversableFactory;

    protected function __constructIterator(callable $groupKeyFunction, callable $traversableFactory)
    {
        $this->groupKeyFunction   = Functions::allowExcessiveArguments($groupKeyFunction);
        $this->traversableFactory = $traversableFactory;
    }
}
