<?php

namespace Pinq\Iterators;

/**
 * Iterates the matching outer value with all the matching inner values in a traversable
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CustomGroupJoinIterator extends CustomJoinIteratorBase
{
    protected function getInnerGroupValuesIterator(callable $innerValueFilterFunction)
    {
        $groupTraversable = new \Pinq\Traversable(array_filter($this->innerValues, $innerValueFilterFunction));

        return new \ArrayIterator([$groupTraversable]);
    }
}
