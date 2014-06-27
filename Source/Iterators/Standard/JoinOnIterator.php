<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinOnIterator extends JoinIterator
{
    use Common\JoinOnIterator;
    
    /**
     * @var OrderedMap
     */
    protected $innerValues;
    
    public function __construct(IIterator $outerIterator, IIterator $innerIterator, callable $filter)
    {
        parent::__construct($outerIterator, $innerIterator);
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
        
        return new FilterIterator(
                $this->innerValues,
                function ($innerValue, $innerKey) use ($filter, $outerKey, $outerValue) {
                    return $filter($outerValue, $innerValue, $outerKey, $innerKey);
                });
    }
}
