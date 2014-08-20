<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\IJoinIterator;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnfilteredJoinIterator extends JoinIterator implements IJoinIterator
{
    /**
     * @var OrderedMap
     */
    protected $innerValues;

    public function filterOn(callable $function)
    {
        return new JoinOnIterator($this->outerIterator, $this->innerIterator, $function);
    }

    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new JoinOnEqualityIterator($this->outerIterator, $this->innerIterator, $outerKeyFunction, $innerKeyFunction);
    }

    protected function doRewind()
    {
        $this->innerValues = new OrderedMap($this->defaultIterator($this->innerIterator));
        parent::doRewind();
    }

    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        return $this->innerValues;
    }
}
