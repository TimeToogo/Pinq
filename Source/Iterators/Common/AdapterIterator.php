<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\Standard\IIterator;

/**
 * Common functionality for the adapter iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
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
        $this->source   = $iterator;
        $this->iterator = $iterator instanceof \Iterator ? $iterator : new \IteratorIterator($iterator);
    }

    /**
     * {@inheritDoc}
     */
    final public function getSourceIterator()
    {
        return $this->source;
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return $this->source instanceof IIterator ? $this->source->isArrayCompatible() : false;
    }
}
