<?php

namespace Pinq\Iterators;

/**
 * Iterates the matching outer value with all the matching inner values in a traversable
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EqualityGroupJoinIterator extends EqualityJoinIteratorBase
{
    protected function GetInnerGroupValueIterator(array $InnerGroup)
    {
        return new \ArrayIterator([new \Pinq\Traversable($InnerGroup)]);
    }
}
