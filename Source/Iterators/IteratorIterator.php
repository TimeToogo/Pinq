<?php

namespace Pinq\Iterators;

/**
 * Base class for wrapper iterators.
 * The native implemenation was producing some weird results, I think it is
 * related to http://blog.ircmaxell.com/2011/10/iteratoriterator-php-inconsistencies.html
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IteratorIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    public function __construct(\Traversable $iterator)
    {
        $this->iterator = \Pinq\Utilities::toIterator($iterator);
    }

    public function getInnerIterator()
    {
        return $this->iterator;
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function rewind()
    {
        return $this->iterator->rewind();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }
}
