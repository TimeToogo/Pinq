<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedIterator;

/**
 * Implementation of the ordered iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OrderedIterator extends LazyIterator implements IOrderedIterator
{
    use Common\OrderedIterator;

    public function __construct(IIterator $iterator, callable $orderByFunction, $isAscending)
    {
        parent::__construct($iterator);
        self::__constructIterator($orderByFunction, $isAscending);
    }

    protected function initializeIterator(IIterator $innerIterator)
    {
        return $this->sortMap(new OrderedMap($innerIterator));
    }
}
