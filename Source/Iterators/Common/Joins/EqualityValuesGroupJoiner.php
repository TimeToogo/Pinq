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
class EqualityValuesGroupJoiner extends EqualityValuesJoiner
{
    /**
     * @var callable
     */
    private $traversableFactory;
    
    public function __construct(
            IIteratorScheme $scheme, 
            callable $outerKeyFunction, 
            callable $innerKeyFunction,
            callable $traversableFactory)
    {
        parent::__construct($scheme, $outerKeyFunction, $innerKeyFunction);
        
        $this->traversableFactory = $traversableFactory;
    }
    
    protected function getInnerGroupValuesIterator(IOrderedMap $innerValuesGroup, $groupKey)
    {
        $traversableFactory = $this->traversableFactory;
        $traversable = $traversableFactory($innerValuesGroup);
        
        return $this->scheme->createOrderedMapFrom([$groupKey], [$traversable]);
    }
}
