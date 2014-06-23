<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupJoinOnIterator extends GroupJoinIterator
{
    use Common\JoinOnIterator;
    
    /**
     * @var OrderedMap
     */
    protected $innerValues;
    
    public function __construct(
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $traversableFactory, 
            callable $filter)
    {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnIterator($filter);
    }
    
    protected function doRewind()
    {
        $this->innerValues = new OrderedMap($this->innerIterator);
        parent::doRewind();
    }

    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        $filter = $this->filter;
        $traversableFactory = $this->traversableFactory;
        
        $innerGroup = $traversableFactory(new FilterIterator(
                $this->innerValues, 
                function ($innerValue, $innerKey) use ($filter, $outerKey, $outerValue) {
                    return $filter($outerValue, $innerValue, $outerKey, $innerKey);
                }));
        
        return new ArrayIterator([0 => $innerGroup]);
    }

}
