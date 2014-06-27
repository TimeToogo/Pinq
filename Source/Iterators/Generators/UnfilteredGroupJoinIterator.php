<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IJoinIterator;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnfilteredGroupJoinIterator extends GroupJoinIterator implements IJoinIterator
{
    public function filterOn(callable $function)
    {
        return new GroupJoinOnIterator(
                $this->outerIterator, 
                $this->innerIterator, 
                $this->traversableFactory, 
                $function);
    }

    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new GroupJoinOnEqualityIterator(
                $this->outerIterator,
                $this->innerIterator, 
                $this->traversableFactory,
                $outerKeyFunction, 
                $innerKeyFunction);
    }
    
    protected function innerGenerator($outerKey, $outerValue)
    {
        $traversableFactory = $this->traversableFactory;
        $innerGroup = $traversableFactory(new OrderedMap($this->defaultIterator($this->innerIterator)));
        
        return new ArrayIterator([0 => $innerGroup]);
    }
}
