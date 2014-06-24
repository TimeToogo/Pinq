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
            IGenerator $outerIterator, 
            IGenerator $innerIterator, 
            callable $traversableFactory, 
            callable $filter)
    {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnIterator($filter);
    }
    
    protected function innerGenerator($outerKey, $outerValue)
    {
        $filter = $this->filter;
        $traversableFactory = $this->traversableFactory;
        $innerValues = new OrderedMap($this->innerIterator);
        
        $innerGroup = $traversableFactory(new FilterIterator(
                $innerValues, 
                function ($innerValue, $innerKey) use ($filter, $outerKey, $outerValue) {
                    return $filter($outerValue, $innerValue, $outerKey, $innerKey);
                }));
        
        return new ArrayIterator([0 => $innerGroup]);
    }
}
