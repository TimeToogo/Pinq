<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\IJoinIterator;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnfilteredGroupJoinIterator extends GroupJoinIterator implements IJoinIterator
{
    /**
     * @var \Pinq\ITraversable
     */
    protected $innerGroup;

    public function filterOn(callable $function)
    {
        return new GroupJoinOnIterator(
                $this->outerIterator,
                $this->innerIterator,
                $this->traversableFactory,
                $function);
    }

    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new GroupJoinOnEqualityIterator(
                $this->outerIterator,
                $this->innerIterator,
                $this->traversableFactory,
                $outerKeyFunction,
                $innerKeyFunction);
    }

    protected function doRewind()
    {
        $traversableFactory = $this->traversableFactory;
        $this->innerGroup   = $traversableFactory(new OrderedMap($this->defaultIterator($this->innerIterator)));
        parent::doRewind();
    }

    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        return new ArrayIterator([0 => $this->innerGroup]);
    }
}
