<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the projection iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProjectionIterator extends IteratorIterator
{
    use Common\ProjectionIterator;

    public function __construct(IIterator $iterator, callable $keyProjectionFunction = null, callable $valueProjectionFunction = null)
    {
        parent::__construct($iterator);
        self::__constructIterator($keyProjectionFunction, $valueProjectionFunction);
    }
    
    protected function doFetch(&$key, &$value)
    {
        if($this->iterator->fetch($key, $value)) {
            $this->projectKeyAndValue($key, $value);
            
            return true;
        }
        
        return false;
    }
}
