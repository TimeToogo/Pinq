<?php

namespace Pinq\Iterators;

class EqualityJoinIterator extends EqualityJoinIteratorBase
{
    protected function GetInnerGroupValueIterator(array $InnerGroup)
    {
        return new \ArrayIterator($InnerGroup);
    }
}
