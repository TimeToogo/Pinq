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
    
    protected function innerGenerator($outerKey, $outerValue)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $groupKey = $outerKeyFunction($outerValue, $outerKey);
        $innerGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        
        return $this->defaultIterator($innerGroups->contains($groupKey) ? 
                $innerGroups->get($groupKey) : new EmptyIterator());
    }
}
