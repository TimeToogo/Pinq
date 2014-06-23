<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupJoinOnIterator extends GroupJoinIterator
{
    use Common\JoinOnIterator;
    
    public function __construct(
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $traversableFactory, 
            callable $filter)
    {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnIterator($filter);
    }
    
    protected function joinGenerator(
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $projectionFunction)
    {
        $filter = $this->filter;
        $traversableFactory = $this->traversableFactory;
        $innerElements = new OrderedMap($innerIterator);
        
        foreach($outerIterator as $outerKey => $outerValue) {
            $innerGroup = $traversableFactory(new FilterIterator(
                    $innerElements, 
                    function ($innerValue, $innerKey) use ($filter, $outerKey, $outerValue) {
                        return $filter($outerValue, $innerValue, $outerKey, $innerKey);
                    }));
            
            yield $projectionFunction($outerValue, $innerGroup, $outerKey, 0);
        }
    }
    
    public function walk(callable $function)
    {
        $outerIterator = $this->iterator;
        $filter = $this->filter;
        $traversableFactory = $this->traversableFactory;
        $innerElements = new OrderedMap($this->innerIterator);
        
        foreach($outerIterator as $outerKey => &$outerValue) {
            $innerGroup = $traversableFactory(new FilterIterator(
                    $innerElements, 
                    function ($innerValue, $innerKey) use ($filter, $outerKey, $outerValue) {
                        return $filter($outerValue, $innerValue, $outerKey, $innerKey);
                    }));
            
            $function($outerValue, $innerGroup, $outerKey, 0);
        }
    }
}
