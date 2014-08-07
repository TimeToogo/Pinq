<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the grouped iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GroupedIterator extends LazyIterator
{
    use Common\GroupedIterator;

    public function __construct(IIterator $iterator, callable $groupKeyFunction, callable $traversableFactory)
    {
        parent::__construct($iterator);
        self::__constructIterator($groupKeyFunction, $traversableFactory);
    }

    protected function initializeIterator(IIterator $innerIterator)
    {
        $groupedMap = (new OrderedMap($innerIterator))->groupBy($this->groupKeyFunction);

        return new ProjectionIterator(
                $groupedMap,
                null,
                $this->traversableFactory);
    }
}
