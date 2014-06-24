<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the adapter iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IteratorAdapter extends Iterator implements \Pinq\Iterators\IAdapterIterator
{
    use Common\AdapterIterator;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct();
        self::__constructIterator($iterator);
    }

    public function doRewind()
    {
        parent::doRewind();
        
        $this->iterator->rewind();
    }
    
    final protected function doFetch()
    {
        $iterator = $this->iterator;
        
        if($iterator->valid()) {
            $element = [$iterator->key(), $iterator->current()];
            
            $iterator->next();
            
            return $element;
        }
    }
}
