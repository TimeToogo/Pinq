<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the filter iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FilterIterator extends IteratorIterator
{
    use Common\FilterIterator;
    
    public function __construct(IIterator $iterator, callable $filter)
    {
        parent::__construct($iterator);
        self::__constructIterator($filter);
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
}
