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
    
    protected function joinGenerator(
            IGenerator $outerIterator, 
            IGenerator $innerIterator, 
            callable $projectionFunction)
    {
        $traversableFactory = $this->traversableFactory;
        $innerGroup = $traversableFactory(new OrderedMap($innerIterator));
        
        foreach($outerIterator as $outerKey => $outerValue) {
            yield $projectionFunction($outerValue, $innerGroup, $outerKey, 0);
        }
    }
    
    public function walk(callable $function)
    {
        $outerIterator = $this->iterator;
        $traversableFactory = $this->traversableFactory;
        $innerGroup = $traversableFactory(new OrderedMap($this->innerIterator));
        
        foreach($outerIterator as $outerKey => &$outerValue) {
            $function($outerValue, $innerGroup, $outerKey, 0);
        }
    }
}
