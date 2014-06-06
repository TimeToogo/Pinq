<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IOrderedMap;

/**
 * Common functionality for the grouped iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
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
        $this->groupKeyFunction = Functions::allowExcessiveArguments($groupKeyFunction);
        $this->traversableFactory = $traversableFactory;
    }
}
