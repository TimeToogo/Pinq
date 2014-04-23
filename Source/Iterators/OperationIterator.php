<?php 

namespace Pinq\Iterators;

/**
 * Base class for a set operation iterator, the other values
 * are stored in a set which can be used to filter the resulting values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class OperationIterator extends IteratorIterator
{
    /**
     * @var \Traversable
     */
    private $otherIterator;
    
    /**
     * @var Utilities\Set
     */
    private $otherValues;
    
    public function __construct(\Traversable $iterator, \Traversable $otherIterator)
    {
        parent::__construct($iterator);
        $this->otherIterator = $otherIterator;
    }
    
    public final function valid()
    {
        while (parent::valid()) {
            if ($this->setFilter(parent::current(), $this->otherValues)) {
                return true;
            }
            
            parent::next();
        }
        
        return false;
    }
    
    protected abstract function setFilter($value, Utilities\Set $otherValues);
    
    public final function rewind()
    {
        $this->otherValues = new Utilities\Set($this->otherIterator);
        parent::rewind();
    }
}