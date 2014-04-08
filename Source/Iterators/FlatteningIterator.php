<?php

namespace Pinq\Iterators;

class FlatteningIterator extends \IteratorIterator
{
    private $Count = 0;
    /**
     * @var \Iterator
     */
    protected $CurrentIterator;
    
    public function __construct(\Traversable $Iterator)
    {
        parent::__construct($Iterator);
        $this->CurrentIterator = new \ArrayIterator();
    }
    
    
    public function current()
    {
        return $this->CurrentIterator->current();
    }

    public function key()
    {
        return $this->Count;
    }

    public function next()
    {
        $this->Count++;
        $this->CurrentIterator->next();
    }
    
    public function valid()
    {
        while(!$this->CurrentIterator->valid()) {
            parent::next();
            
            if(!parent::valid()) {
                return false;
            }
            
            $this->LoadCurrentIterator();
        }
        
        return true;
    }
    
    private function LoadCurrentIterator() {
        $this->CurrentIterator = \Pinq\Utilities::ToIterator(parent::current());
        
        $this->CurrentIterator->rewind();
    }
    
    public function rewind()
    {
        $this->Count = 0;
        parent::rewind();
        $this->LoadCurrentIterator();
    }
}
