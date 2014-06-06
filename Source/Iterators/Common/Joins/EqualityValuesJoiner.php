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
class EqualityValuesJoiner extends InnerValuesJoiner
{
    /**
     * @var callable
     */
    private $outerKeyFunction;

    /**
     * @var callable
     */
    private $innerKeyFunction;

    /**
     * @var IOrderedMap
     */
    private $innerValueGroups;

    public function __construct(
            IIteratorScheme $scheme,
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        parent::__construct($scheme);
        $this->outerKeyFunction = Common\Functions::allowExcessiveArguments($outerKeyFunction);
        $this->innerKeyFunction = Common\Functions::allowExcessiveArguments($innerKeyFunction);
    }
    
    final public function initialize(\Traversable $innerValuesIterator)
    {
        $this->innerValueGroups = $this->scheme
                ->createOrderedMap($innerValuesIterator)
                ->groupBy($this->innerKeyFunction);
    }
    
    final public function getInnerGroupIterator($outerValue, $outerKey)
    {        
        $outerEqualityValueFunction = $this->outerKeyFunction;
        $groupKey = $outerEqualityValueFunction($outerValue, $outerKey);

        if ($this->innerValueGroups->contains($groupKey)) {
            $currentInnerGroup = $this->innerValueGroups->get($groupKey);
        } else {
            $currentInnerGroup = $this->scheme->createOrderedMap();
        }

        return $this->getInnerGroupValuesIterator($currentInnerGroup, $groupKey);
    }
    
    protected function getInnerGroupValuesIterator(IOrderedMap $innerValuesGroup, $groupKey)
    {
        return $innerValuesGroup;
    }
}
