<?php

namespace Pinq\Iterators;

class CustomGroupJoinIterator extends CustomJoinIteratorBase
{
    protected function GetInnerGroupValuesIterator(callable $InnerValueFilterFunction)
    {
        $GroupTraversable = new \Pinq\Traversable(array_filter($this->InnerValues, $InnerValueFilterFunction));
        
        return new \ArrayIterator([$GroupTraversable]);
    }
}
