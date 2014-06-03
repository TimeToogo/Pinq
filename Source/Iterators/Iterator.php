<?php

namespace Pinq\Iterators;

/**
 * Base class for iterators, simplifies value fetching / validating into a single step.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Iterator implements \Iterator
{
    /**
     * @var mixed
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var boolean
     */
    protected $valid = false;

    final public function valid()
    {
        return $this->valid;
    }

    final public function key()
    {
        return $this->key;
    }

    final public function current()
    {
        return $this->value;
    }
    
    private function invalidate()
    {
        $this->valid = false;
        $this->key = null;
        $this->value = null;
    }

    final public function rewind()
    {
        $this->invalidate();
        $this->onRewind();
        $this->valid = $this->fetch($this->key, $this->value);
    }
    
    protected function onRewind()
    {
        
    }

    final public function next()
    {
        $this->invalidate();
        $this->onNext();
        $this->valid = $this->fetch($this->key, $this->value);
    }
    
    protected function onNext()
    {
        
    }
    
    /**
     * @return boolean
     */
    protected abstract function fetch(&$key, &$value);
}
