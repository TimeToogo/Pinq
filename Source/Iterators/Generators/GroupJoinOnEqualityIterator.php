<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupJoinOnEqualityIterator extends GroupJoinIterator
{
    use Common\JoinOnEqualityIterator;
    
    public function __construct(
            \Traversable $outerIterator,
            \Traversable $innerIterator, 
            callable $traversableFactory, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }
    
    protected function joinGenerator(
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $projectionFunction)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $traversableFactory = $this->traversableFactory;
        $innerGroups = (new OrderedMap($innerIterator))->groupBy($this->innerKeyFunction);
        
        foreach($outerIterator as $outerKey => $outerValue) {
            $groupKey = $outerKeyFunction($outerValue, $outerKey);
            
            $group = $traversableFactory($innerGroups->contains($groupKey) ? $innerGroups->get($groupKey) : []);
            
            yield $projectionFunction($outerValue, $group, $outerKey, $groupKey);
        }
    }
    
    public function walk(callable $function)
    {
        $outerIterator = $this->iterator;
        $outerKeyFunction = $this->outerKeyFunction;
        $traversableFactory = $this->traversableFactory;
        $innerGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        
        foreach($outerIterator as $outerKey => &$outerValue) {
            $groupKey = $outerKeyFunction($outerValue, $outerKey);
            
            $group = $traversableFactory($innerGroups->contains($groupKey) ? $innerGroups->get($groupKey) : []);
            
            $function($outerValue, $group, $outerKey, $groupKey);
        }
    }
}
