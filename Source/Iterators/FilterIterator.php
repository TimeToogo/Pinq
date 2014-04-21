<?php

namespace Pinq\Iterators;

/**
 * Returns the values that satisfy the supplied predicate function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FilterIterator extends IteratorIterator
{
    /**
     * @var callable
     */
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
