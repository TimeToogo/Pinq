<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedIterator;

/**
 * Implementation of the ordered iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OrderedIterator extends LazyGenerator implements IOrderedIterator
{
    use Common\OrderedIterator;

    public function __construct(IGenerator $iterator, callable $orderByFunction, $isAscending)
    {
        parent::__construct($iterator);
        self::__constructIterator($orderByFunction, $isAscending);
    }

    protected function initializeGenerator(IGenerator $iterator)
    {
        return $this->sortMap(new OrderedMap($iterator));
    }
}
