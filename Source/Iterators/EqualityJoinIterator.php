<?php 

namespace Pinq\Iterators;

/**
 * Iterates the matching outer value with every matching inner value individually
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EqualityJoinIterator extends EqualityJoinIteratorBase
{
    protected function getInnerGroupValueIterator(array $innerGroup)
    {
        return new \ArrayIterator($innerGroup);
    }
}