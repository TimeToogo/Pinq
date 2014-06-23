<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the group join iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait GroupJoinIterator
{
    /**
     * @var callable
     */
    protected $traversableFactory;

    protected function __constructGroupJoinIterator(callable $traversableFactory)
    {
        $this->traversableFactory = $traversableFactory;
    }
}
