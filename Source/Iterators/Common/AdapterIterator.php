<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the adapeter iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait AdapterIterator
{
    /**
     * @var \Traversable
     */
    protected $source;
    
    /**
     * @var \Iterator
     */
    protected $iterator;

    public function __constructIterator(\Traversable $iterator)
    {
        $this->source = $iterator;
        $this->iterator = $iterator instanceof \Iterator ? $iterator : new \IteratorIterator($iterator);
    }
    
    /**
     * {@inheritDoc}
     */
    final public function getSourceIterator()
    {
        return $this->source;
    }
}
