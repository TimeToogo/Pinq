<?php

namespace Pinq\Iterators;

class CustomJoinIterator extends CustomJoinIteratorBase
{
    protected function GetInnerGroupIterator($OuterValue)
    {
        $JoinOnFunction = $this->JoinOnFunction;
        
        return new FilterIterator(new \ArrayIterator($this->InnerValues), 
                function ($InnerValue) use ($OuterValue, $JoinOnFunction) {
                    return $JoinOnFunction($OuterValue, $InnerValue);
                });
    }
}
