<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIterator;

/**
 * Common functionality for the group join iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
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

    protected function constructInnerGroup(\Traversable $innerElements)
    {
        $traversableFactory = $this->traversableFactory;

        return $traversableFactory($innerElements);
    }
}
