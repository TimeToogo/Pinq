<?php

namespace Pinq\Iterators;

/**
 * Iterates the matching outer value with every matching inner value individually
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CustomJoinIterator extends CustomJoinIteratorBase
{
    protected function getInnerGroupValuesIterator(callable $innerValueFilterFunction)
    {
        return new FilterIterator(
                new \ArrayIterator($this->innerValues),
                $innerValueFilterFunction);
    }
}
