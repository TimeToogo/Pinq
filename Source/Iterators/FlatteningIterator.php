<?php 

namespace Pinq\Iterators;

/**
 * Iterates the inner iterator and for every value, it is then iterated as
 * the resulting values (pretty much a nested foreach loop)
 * 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FlatteningIterator extends IteratorIterator
{
    private $count = 0;
    
    /**
     * @var \Iterator
     */
    protected $currentIterator;
    
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
        $this->currentIterator = new \ArrayIterator([]);
    }
    
    public function current()
    {
        return $this->currentIterator->current();
    }
    
    public function key()
    {
        return $this->count;
    }
    
    public function next()
    {
        $this->count++;
        $this->currentIterator->next();
    }
    
    public function valid()
    {
        while (!$this->currentIterator->valid()) {
            parent::next();
            
            if (!parent::valid()) {
                return false;
            }
            
            $this->loadCurrentIterator();
        }
        
        return true;
    }
    
    private function loadCurrentIterator()
    {
        $this->currentIterator = \Pinq\Utilities::toIterator(parent::current());
        $this->currentIterator->rewind();
    }
    
    public function rewind()
    {
        $this->count = 0;
        parent::rewind();
        $this->loadCurrentIterator();
    }
}