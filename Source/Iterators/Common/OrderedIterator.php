<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIterator;
use Pinq\Iterators\IOrderedIterator;
use Pinq\Iterators\IOrderedMap;

/**
 * Common functionality for the ordered iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait OrderedIterator
{
    /**
     * @var callable[]
     */
    protected $orderByFunctions = [];

    /**
     * @var boolean[]
     */
    protected $isAscendingArray = [];

    protected function __constructIterator(callable $orderByFunction, $isAscending)
    {
        $this->orderByFunctions[] = Functions::allowExcessiveArguments($orderByFunction);
        $this->isAscendingArray[] = $isAscending;
    }

    /**
     * @param callable $orderByFunction
     * @param boolean  $isAscending
     *
     * @return IOrderedIterator
     */
    final public function thenOrderBy(callable $orderByFunction, $isAscending)
    {
        $newOrderedIterator = new self($this->getSourceIterator(), function () {
        }, true);

        $newOrderedIterator->orderByFunctions   = $this->orderByFunctions;
        $newOrderedIterator->isAscendingArray   = $this->isAscendingArray;
        $newOrderedIterator->orderByFunctions[] = Functions::allowExcessiveArguments($orderByFunction);
        $newOrderedIterator->isAscendingArray[] = $isAscending;

        return $newOrderedIterator;
    }

    /**
     * @return IIterator
     */
    abstract protected function getSourceIterator();

    final protected function sortMap(IOrderedMap $map)
    {
        return $map->multisort($this->orderByFunctions, $this->isAscendingArray);
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return  $this->getSourceIterator()->isArrayCompatible();
    }
}
