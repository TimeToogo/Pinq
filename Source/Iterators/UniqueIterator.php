<?php

namespace Pinq\Iterators;

class UniqueIterator extends \IteratorIterator
{
    private $SeenValues = [];
    
    public function rewind()
    {
        $this->SeenValues = [];
        parent::rewind();
    }
    
    public function valid()
    {
        while(parent::valid()) {
            $CurrentValue = self::current();
            
            if(!in_array($CurrentValue, $this->SeenValues, true)) {
                $this->SeenValues[] = $CurrentValue;
                return true;
            }
            
            parent::next();
        }
        return false;
    }
}
