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
            IGenerator $outerIterator,
            IGenerator $innerIterator, 
            callable $traversableFactory, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }
    
    protected function innerGenerator($outerKey, $outerValue)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $traversableFactory = $this->traversableFactory;
        $groupKey = $outerKeyFunction($outerValue, $outerKey);
        
        $innerGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        
        $traversableGroup = $traversableFactory($innerGroups->contains($groupKey) ? 
                $innerGroups->get($groupKey) : []);
        
        return new ArrayIterator([$groupKey => $traversableGroup]);
    }
}
