<?php

namespace Pinq\Iterators;

class UniqueIterator extends IteratorIterator
{
    /**
     * @var HashSet 
     */
    private $SeenValues;
    
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
        $this->SeenValues = new HashSet();
    }
    
    public function rewind()
    {
        $this->SeenValues = new HashSet();
        parent::rewind();
    }
    
    public function valid()
    {
        while(parent::valid()) {
            $CurrentValue = self::current();
            
            if($this->SeenValues->Add($CurrentValue)) {
                return true;
            }
            
            parent::next();
        }
        return false;
    }
}
