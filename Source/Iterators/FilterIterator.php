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
    private $filter;

    public function __construct(\Traversable $iterator, callable $filter)
    {
        parent::__construct($iterator);
        
        $this->filter = Utilities\Functions::allowExcessiveArguments($filter);
    }
    
    protected function doFetch(&$key, &$value)
    {
        $filter = $this->filter;
        
        while($this->iterator->fetch($key, $value)) {
            $keyCopy = $key;
            $valueCopy = $value;
            
            if ($filter($valueCopy, $keyCopy)) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function fetchInner(\Iterator $iterator, &$key, &$value)
    {
        $filter = $this->filter;
        
        while(parent::fetchInner($iterator, $key, $value)) {
            $keyCopy = $key;
            $valueCopy = $value;
            
            if ($filter($valueCopy, $keyCopy)) {
                return true;
            }
            
            $iterator->next();
        }
        
        return false;
    }
}
