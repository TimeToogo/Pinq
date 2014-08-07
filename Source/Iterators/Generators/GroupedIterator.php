<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the grouped iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GroupedIterator extends LazyGenerator
{
    use Common\GroupedIterator;

    public function __construct(IGenerator $iterator, callable $groupByFunction, callable $traversableFactory)
    {
        parent::__construct($iterator);
        self::__constructIterator($groupByFunction, $traversableFactory);
    }

    protected function initializeGenerator(IGenerator $innerIterator)
    {
        $groupedMap = (new OrderedMap($innerIterator))->groupBy($this->groupKeyFunction);

        return new ProjectionIterator(
                $groupedMap,
                null,
                $this->traversableFactory);
    }
}
