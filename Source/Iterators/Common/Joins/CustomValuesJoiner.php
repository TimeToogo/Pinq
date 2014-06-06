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
class CustomValuesJoiner extends InnerValuesJoiner
{
    /**
     * @var callable
     */
    protected $joinOnFunction;

    /**
     * @var IOrderedMap
     */
    protected $innerValues;

    public function __construct(IIteratorScheme $scheme, callable $joinOnFunction)
    {
        parent::__construct($scheme);
        $this->joinOnFunction = Common\Functions::allowExcessiveArguments($joinOnFunction);
    }
    
    final public function initialize(\Traversable $innerValuesIterator)
    {
        $this->innerValues = $this->scheme->createOrderedMap($innerValuesIterator);
    }

    final public function getInnerGroupIterator($outerValue, $outerKey)
    {
        $joinOnFunction = $this->joinOnFunction;
        $innerValueFilterFunction = function ($innerValue, $innerKey) use ($outerValue, $outerKey, $joinOnFunction) {
            return $joinOnFunction($outerValue, $innerValue, $outerKey, $innerKey);
        };

        return $this->getInnerGroupValuesIterator($this->innerValues, $innerValueFilterFunction);
    }
    
    protected function getInnerGroupValuesIterator(IOrderedMap $innerValues, callable $innerValueFilterFunction)
    {
        return $this->scheme->filterIterator($innerValues, $innerValueFilterFunction);
    }
}
