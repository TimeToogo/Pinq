<?php

namespace Pinq\Iterators\Common\Joins;

use Pinq\Iterators\Common;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\IOrderedMap;

/**
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CustomValuesGroupJoiner extends CustomValuesJoiner
{
    /**
     * @var callable
     */
    private $traversableFactory;
    
    public function __construct(
            IIteratorScheme $scheme, 
            callable $joinOnFunction, 
            callable $traversableFactory)
    {
        parent::__construct($scheme, $joinOnFunction);
        
        $this->traversableFactory = $traversableFactory;
    }
    
    protected function getInnerGroupValuesIterator(IOrderedMap $innerValues, callable $innerValueFilterFunction)
    {
        $traversableFactory = $this->traversableFactory;
        $traversable = $traversableFactory(parent::getInnerGroupValuesIterator($innerValues, $innerValueFilterFunction));
        
        return $this->scheme->arrayIterator([0 => $traversable]);
    }
}
