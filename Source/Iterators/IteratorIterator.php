<?php

namespace Pinq\Iterators;

/**
 * Base class for wrapper iterators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IteratorIterator extends Iterator implements \Iterator
{
    /**
     * @var \Pinq\IIterator
     */
    protected $iterator;

    public function __construct(\Traversable $iterator)
    {
        $this->iterator = \Pinq\Utilities::toIterator($iterator);
    }
    
    /**
     * @return Pinq\IIterator
     */
    final public function getInnerIterator()
    {
        return $this->iterator;
    }

    public function doRewind()
    {
        $this->iterator->rewind();
    }
}
