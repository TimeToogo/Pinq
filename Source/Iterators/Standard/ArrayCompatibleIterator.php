<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the array compatible iterator using the fetch method.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayCompatibleIterator extends IteratorIterator
{
    use Common\ArrayCompatibleIterator;
    
    /**
     * @var int
     */
    private $maxKey = 0;
    
    /**
     * @var Common\OrderedMap
     */
    private $nonScalarKeyMap;
    
    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
    }
    
    public function doRewind()
    {
        $this->maxKey = 0;
        $this->nonScalarKeyMap = new OrderedMap();
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value) {
        
        if($this->iterator->fetch($key, $value)) {
            $this->makeKeyCompatible($key, $this->maxKey, $this->nonScalarKeyMap);
            
            return true;
        }
        
        return false;
    }
}
