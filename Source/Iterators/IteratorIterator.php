<?php

namespace Pinq\Iterators;

class IteratorIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $Iterator;
    
    public function __construct(\Traversable $Iterator)
    {
        if($Iterator instanceof \IteratorAggregate) {
            $this->Iterator = $Iterator->getIterator();
        }
        else if($Iterator instanceof \Iterator) {
            $this->Iterator = $Iterator;
        }
        else {
            $this->Iterator = new \Pinq\Iterators\IteratorIterator($Iterator);
        }
    }

    
    public function current()
    {
        return $this->Iterator->current();
    }

    public function key()
    {
        return $this->Iterator->key();
    }

    public function next()
    {
        return $this->Iterator->next();
    }

    public function rewind()
    {
        return $this->Iterator->rewind();
    }

    public function valid()
    {
        return $this->Iterator->valid();
    }
}
