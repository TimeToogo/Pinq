<?php

namespace Pinq\Iterators;

class CustomJoinIterator extends CustomJoinIteratorBase
{
    protected function GetInnerGroupValuesIterator(callable $InnerValueFilterFunction)
    {
        return new FilterIterator(new \ArrayIterator($this->InnerValues), $InnerValueFilterFunction);
    }
}
