<?php

namespace Pinq\Iterators;

class EqualityGroupJoinIterator extends EqualityJoinIteratorBase
{
    protected function GetInnerGroupValueIterator(array $InnerGroup)
    {
        return new \ArrayIterator([new \Pinq\Traversable($InnerGroup)]);
    }
}
