<?php

namespace Pinq\Iterators;

/**
 * Iterator adapter class for native iterators
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IteratorAdapter extends Iterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    public function __construct(\Traversable $iterator)
    {
        $this->iterator = $iterator instanceof \Iterator ? $iterator : new \IteratorIterator($iterator);
    }
    
    /**
     * @return \Iterator
     */
    final public function getInnerIterator()
    {
        return $this->iterator;
    }

    public function doRewind()
    {
        $this->iterator->rewind();
    }
    
    final protected function doFetch(&$key, &$value)
    {
        $iterator = $this->iterator;
        
        if($iterator->valid()) {
            $key = $iterator->key();
            $value = $iterator->current();
            
            $iterator->next();
            
            return true;
        }
        
        return false;
    }
}
