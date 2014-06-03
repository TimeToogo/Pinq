<?php

namespace Pinq\Iterators;

/**
 * Iterates the unique values in the orignal values
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UniqueIterator extends OperationIterator
{
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator, new \EmptyIterator());
    }

    protected function setFilter($key, $value, Utilities\Set $seenValues)
    {
        return $seenValues->add($value);
    }
}
