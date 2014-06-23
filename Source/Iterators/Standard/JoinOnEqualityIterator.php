<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinOnEqualityIterator extends JoinIterator
{
    use Common\JoinOnEqualityIterator;
    
    /**
     * @var OrderedMap
     */
    protected $innerValuesGroups;
    
    public function __construct(
            \Traversable $outerIterator,
            \Traversable $innerIterator, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }
    
    protected function doRewind()
    {
        $this->innerValuesGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        parent::doRewind();
    }
    
    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $groupKey = $outerKeyFunction($outerValue, $outerKey);
        
        return $this->innerValuesGroups->contains($groupKey) ? 
                $this->innerValuesGroups->get($groupKey) : new EmptyIterator();
    }
    
}
