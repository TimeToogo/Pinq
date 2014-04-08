<?php

namespace Pinq\Iterators;

class UnionIterator extends FlatteningIterator
{
    private $Count = 0;
    /**
     * @var HashSet
     */
    private $SeenValues;
    
    public function __construct(\Traversable $Iterator, \Traversable $OtherIterator)
    {
        parent::__construct(new \ArrayIterator([$Iterator, $OtherIterator]));
        $this->SeenValues = new HashSet();
    }
    
    public function key()
    {
        return $this->Count;
    }
    
    public function rewind()
    {
        $this->SeenValues = new HashSet();
        $this->Count = 0;
        parent::rewind();
    }
    
    public function next()
    {
        $this->Count++;
        parent::next();
    }
    
    public function valid()
    {
        while(parent::valid()) {
            if($this->SeenValues->Add(parent::current())) {
                return true;
            }
            
            $this->next();
        }
        
        return false;
    }
}
