<?php

namespace Pinq\Iterators;

class FilterIterator extends \IteratorIterator
{
    private $Filter;
    
    public function __construct(\Traversable $Iterator, callable $Filter)
    {
        parent::__construct($Iterator);
        $this->Filter = $Filter;
    }
    
    public function valid()
    {
        $Filter = $this->Filter;
        while(parent::valid()) {
            $CurrentValue = self::current();
            
            if($Filter($CurrentValue)) {
                return true;
            }
            
            parent::next();
        }
        return false;
    }
}
