<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IJoinIterator;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnfilteredJoinIterator extends JoinIterator implements IJoinIterator
{
    public function filterOn(callable $function)
    {
        return new JoinOnIterator(
                $this->outerIterator, 
                $this->innerIterator, 
                $function);
    }

    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new JoinOnEqualityIterator(
                $this->outerIterator, 
                $this->innerIterator, 
                $outerKeyFunction, 
                $innerKeyFunction);
    }
    
    protected function joinGenerator(
            IGenerator $outerIterator, 
            IGenerator $innerIterator, 
            callable $projectionFunction)
    {
        $innerIterator = new OrderedMap($this->innerIterator);
        
        foreach($outerIterator as $outerKey => $outerValue) {
            foreach($innerIterator as $innerKey => $innerValue) {
                yield $projectionFunction($outerValue, $innerValue, $outerKey, $innerKey);
            }
        }
    }
    
    public function walk(callable $function)
    {
        $outerIterator = $this->iterator;
        $innerIterator = new OrderedMap($this->innerIterator);
        
        foreach($outerIterator as $outerKey => &$outerValue) {
            foreach($innerIterator as $innerKey => &$innerValue) {
                $function($outerValue, $innerValue, $outerKey, $innerKey);
            }
        }
    }
}
