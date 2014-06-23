<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the join on iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
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
}
