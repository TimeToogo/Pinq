<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the adapter iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IteratorAdapter extends Iterator implements \OuterIterator
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct();
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
