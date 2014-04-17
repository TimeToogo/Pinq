<?php

namespace Pinq\Iterators;

class CustomGroupJoinIterator extends CustomJoinIteratorBase
{    
    protected function GetInnerGroupIterator($OuterValue)
    {
        $JoinOnFunction = $this->JoinOnFunction;
        
        $GroupTraversable = new \Pinq\Traversable(\array_filter($this->InnerValues, 
                            function ($InnerValue) use ($OuterValue, $JoinOnFunction) {
                                return $JoinOnFunction($OuterValue, $InnerValue);
                            }));
                            
        return new \ArrayIterator([$GroupTraversable]);
    }
}
