<?php

namespace Pinq\Iterators;

class UniqueIterator extends IteratorIterator
{
    /**
     * @var Utilities\Set 
     */
    private $SeenValues;
    
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
        $this->SeenValues = new Utilities\Set();
    }
    
    public function rewind()
    {
        $this->SeenValues = new Utilities\Set();
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
