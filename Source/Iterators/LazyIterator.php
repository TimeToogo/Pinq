<?php 

namespace Pinq\Iterators;

/**
 * Basy class for a lazy iterator, the initializer will be
 * called once, when first accessed.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class LazyIterator extends IteratorIterator
{
    /**
     * @var \Traversable
     */
    protected $iterator;
    
    /**
     * @var boolean
     */
    private $isInitialized = false;
    
    public function __construct(\Traversable $iterator)
    {
        $this->iterator = $iterator;
    }
    
    private function initialize()
    {
        $iterator = $this->initializeIterator($this->iterator) ?: $this->iterator;
        parent::__construct($iterator);
        $this->isInitialized = true;
    }
    
    protected abstract function initializeIterator(\Traversable $innerIterator);
    
    public function getInnerIterator()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        return parent::getInnerIterator();
    }
    
    public function current()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        return parent::current();
    }
    
    public function key()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        return parent::key();
    }
    
    public function next()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        return parent::next();
    }
    
    public function rewind()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        return parent::rewind();
    }
}