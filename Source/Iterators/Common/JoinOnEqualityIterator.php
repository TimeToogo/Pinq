<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the join on equality iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait JoinOnEqualityIterator
{
    /**
     * @var callable
     */
    protected $outerKeyFunction;

    /**
     * @var callable
     */
    protected $innerKeyFunction;

    public function __constructJoinOnEqualityIterator(
            callable $outerKeyFunction,
            callable $innerKeyFunction
    ) {
        $this->outerKeyFunction = Functions::allowExcessiveArguments($outerKeyFunction);
        $this->innerKeyFunction = Functions::allowExcessiveArguments($innerKeyFunction);
    }
}
