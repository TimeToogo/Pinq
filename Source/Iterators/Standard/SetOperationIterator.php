<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\Common\SetOperations\ISetFilter;

/**
 * Implementation of the set operation iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SetOperationIterator extends IteratorIterator
{
    use Common\SetOperations\SetOperationIterator;
    
    public function __construct(IIterator $iterator, ISetFilter $setFilter)
    {
        parent::__construct($iterator);
        self::__constructIterator($setFilter);
    }
    
    final public function doRewind()
    {
        $this->setFilter->initialize();
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        while($this->iterator->fetch($key, $value)) {
            if($this->setFilter->filter($key, $value)) {
                return true;
            }
        }
        
        return false;
    }

}
