<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinOnIterator extends JoinIterator
{
    use Common\JoinOnIterator;
    
    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $filter)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructJoinOnIterator($filter);
    }
    
    protected function joinGenerator(
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $projectionFunction)
    {
        $filter = $this->filter;
        $innerElements = new OrderedMap($innerIterator);
        
        foreach($outerIterator as $outerKey => $outerValue) {
            foreach($innerElements as $innerKey => $innerValue) {
                if($filter($outerValue, $innerValue, $outerKey, $innerKey)) {
                    yield $projectionFunction($outerValue, $innerValue, $outerKey, $innerKey);
                }
            }
        }
    }
    
    public function walk(callable $function)
    {
        $outerIterator = $this->iterator;
        $filter = $this->filter;
        $innerIterator = new OrderedMap($this->innerIterator);
        
        foreach($outerIterator as $outerKey => &$outerValue) {
            $outerValueCopy = $outerValue;
            foreach($innerIterator as $innerKey => &$innerValue) {
                $innerValueCopy = $innerValue;
                
                if($filter($outerValueCopy, $innerValueCopy, $outerKey, $innerKey)) {
                    $function($outerValue, $innerValue, $outerKey, $innerKey);
                }
            }
        }
    }
}
