<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinOnEqualityIterator extends JoinIterator
{
    use Common\JoinOnEqualityIterator;
    
    public function __construct(
            IGenerator $outerIterator,
            IGenerator $innerIterator, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }
    
    protected function joinGenerator(
            IGenerator $outerIterator, 
            IGenerator $innerIterator, 
            callable $projectionFunction)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $innerGroups = (new OrderedMap($innerIterator))->groupBy($this->innerKeyFunction);
        
        foreach($outerIterator as $outerKey => $outerValue) {
            $groupKey = $outerKeyFunction($outerValue, $outerKey);
            
            if($innerGroups->contains($groupKey)) {
                foreach($innerGroups->get($groupKey) as $innerKey => $innerValue) {
                    yield $projectionFunction($outerValue, $innerValue, $outerKey, $innerKey);
                }
            }
        }
    }
    
    public function walk(callable $function)
    {
        $outerIterator = $this->iterator;
        $outerKeyFunction = $this->outerKeyFunction;
        $innerGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        
        foreach($outerIterator as $outerKey => &$outerValue) {
            $groupKey = $outerKeyFunction($outerValue, $outerKey);
            
            if($innerGroups->contains($groupKey)) {
                foreach($innerGroups->get($groupKey) as $innerKey => &$innerValue) {
                    $function($outerValue, $innerValue, $outerKey, $innerKey);
                }
            }
        }
    }
}
