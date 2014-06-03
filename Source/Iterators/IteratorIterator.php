<?php

namespace Pinq\Iterators;

/**
 * Base class for wrapper iterators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IteratorIterator extends Iterator implements \Iterator
{
    /**
     * @var Iterator
     */
    private $iterator;

    public function __construct(\Traversable $iterator)
    {
        $this->iterator = \Pinq\Utilities::toIterator($iterator);
    }

    final public function getInnerIterator()
    {
        return $this->iterator;
    }

    public function onRewind()
    {
        $this->iterator->rewind();
    }

    public function onNext()
    {
        $this->iterator->next();
    }
    
    final protected function fetch(&$key, &$value)
    {
        return $this->fetchInner($this->iterator, $key, $value);
    }
    
    protected function fetchInner(\Iterator $iterator, &$key, &$value)
    {
        $valid = $iterator->valid();
        
        if($valid) {
            $key = $iterator->key();
            $value = $iterator->current();
        }
        
        return $valid;
    }
}
