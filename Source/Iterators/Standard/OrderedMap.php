<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\IOrderedMap;
use Pinq\Iterators\Common;

/**
 * Implementation of the ordered map using the fetch method for iteration.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderedMap extends Iterator implements IOrderedMap
{
    use Common\OrderedMap;
    
    /**
     * @var int
     */
    private $position = 0;
    
    public function __construct(IIterator $iterator = null)
    {
        parent::__construct();
        
        if($iterator !== null) {
            $iterator->rewind();
            while ($iterator->fetch($key, $value)) {
                $this->set($key, $value);
            }
        }
    }
    
    protected function doRewind()
    {
        $this->position = 0;
    }
    
    protected function doFetch(&$key, &$value)
    {
        if(isset($this->keys[$this->position]) || array_key_exists($this->position, $this->keys)) {
            $key = $this->keys[$this->position];
            $value = $this->values[$this->position];
            $this->position++;
            
            return true;
        }
        
        return false;
    }
}
