<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupJoinOnEqualityIterator extends GroupJoinIterator
{
    use Common\JoinOnEqualityIterator;
    
    /**
     * @var OrderedMap
     */
    protected $innerGroups;
    
    public function __construct(
            IIterator $outerIterator,
            IIterator $innerIterator, 
            callable $traversableFactory, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }
    
    protected function doRewind()
    {
        $this->innerGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        parent::doRewind();
    }
    
    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $traversableFactory = $this->traversableFactory;
        $groupKey = $outerKeyFunction($outerValue, $outerKey);
        
        $traversableGroup = $traversableFactory($this->innerGroups->contains($groupKey) ? 
                $this->innerGroups->get($groupKey) : []);
        
        return new ArrayIterator([$groupKey => $traversableGroup]);
    }

}
