<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\ISet;
use Pinq\Iterators\IOrderedMap;

/**
 * Contains the common functionality for a ISet implementation
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait Set
{
    /**
     * The array containing the values keyed by their identity hash.
     *
     * @var array
     */
    protected $values = [];
    
    /**
     * The amount of values in the set.
     *
     * @var int
     */
    protected $length = 0;
    
    
    public function count()
    {
        return $this->length;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->values = [];
        $this->length = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function contains($value)
    {
        return isset($this->values[Identity::hash($value)]);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $identityHash = Identity::hash($value);
        
        if(array_key_exists($identityHash, $this->values)) {
            return false;
        }
        
        $this->values[$identityHash] = $value;
        $this->length++;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($value)
    {
        $identityHash = Identity::hash($value);
        
        if(!array_key_exists($identityHash, $this->values)) {
            return false;
        }
        
        unset($this->values[$identityHash]);
        $this->length--;
        return true;
    }
}
