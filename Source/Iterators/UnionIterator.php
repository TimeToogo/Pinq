<?php 

namespace Pinq\Iterators;

/**
 * Iterates the unique values contained in either the first values or in the second values.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnionIterator extends FlatteningIterator
{
    /**
     * @var int
     */
    private $count = 0;
    
    /**
     * @var Utilities\Set
     */
    private $seenValues;
    
    public function __construct(\Traversable $iterator, \Traversable $otherIterator)
    {
        parent::__construct(new \ArrayIterator([$iterator, $otherIterator]));
        $this->seenValues = new Utilities\Set();
    }
    
    public function key()
    {
        return $this->count;
    }
    
    public function rewind()
    {
        $this->seenValues = new Utilities\Set();
        $this->count = 0;
        parent::rewind();
    }
    
    public function next()
    {
        $this->count++;
        parent::next();
    }
    
    public function valid()
    {
        while (parent::valid()) {
            if ($this->seenValues->add(parent::current())) {
                return true;
            }
            
            parent::next();
        }
        
        return false;
    }
}