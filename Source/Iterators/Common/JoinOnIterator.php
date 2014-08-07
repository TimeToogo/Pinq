<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the join on iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait JoinOnIterator
{
    /**
     * @var callable
     */
    protected $filter;

    public function __constructJoinOnIterator(callable $filter)
    {
        $this->filter = Functions::allowExcessiveArguments($filter);
    }

    final protected function innerElementFilter($outerKey, $outerValue)
    {
        $filter = $this->filter;

        return function ($innerValue, $innerKey) use ($filter, $outerKey, $outerValue) {
            return $filter($outerValue, $innerValue, $outerKey, $innerKey);
        };
    }
}
