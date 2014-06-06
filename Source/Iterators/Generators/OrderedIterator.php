<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedIterator;

/**
 * Implementation of the ordered iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderedIterator extends LazyGenerator implements IOrderedIterator
{
    use Common\OrderedIterator;

    public function __construct(\Traversable $iterator, callable $orderByFunction, $isAscending)
    {
        parent::__construct($iterator);
        self::__constructIterator($orderByFunction, $isAscending);
    }

    protected function initializeGenerator(\Traversable $iterator)
    {
        return $this->sortMap(new OrderedMap($iterator));
    }
}
