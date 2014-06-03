<?php

namespace Pinq\Iterators;

/**
 * Base class for iterators
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Iterator implements \Pinq\IIterator
{
    /**
     * @var mixed
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var boolean
     */
    private $valid = false;

    /**
     * @var boolean
     */
    private $requiresFirstFetch = false;

    final public function valid()
    {
        if($this->requiresFirstFetch) {
            $this->fetchInternal();
        }
        
        return $this->valid;
    }

    final public function key()
    {
        if($this->requiresFirstFetch) {
            $this->fetchInternal();
        }
        
        return $this->key;
    }

    final public function current()
    {
        if($this->requiresFirstFetch) {
            $this->fetchInternal();
        }
        
        return $this->value;
    }

    final public function rewind()
    {
        $this->valid = false;
        $this->doRewind();
        $this->requiresFirstFetch = true;
    }
    
    protected function doRewind()
    {
        
    }

    final public function next()
    {
        if($this->requiresFirstFetch) {
            $this->fetchInternal();
        }
        
        $this->fetchInternal();
    }
    
    private function fetchInternal()
    {
        $this->requiresFirstFetch = false;
        $this->valid = $this->doFetch($this->key, $this->value);
    }
    
    final public function fetch(&$key, &$value)
    {
        $this->requiresFirstFetch = false;
        if($this->valid = $this->doFetch($key, $value)) {
            $this->key = $key;
            $this->value = $value;
            
            return true;
        }
        
        return false;
    }
    
    protected abstract function doFetch(&$key, &$value);
}
