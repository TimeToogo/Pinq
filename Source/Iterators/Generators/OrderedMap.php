<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\IOrderedMap;
use Pinq\Iterators\Common;

/**
 * Implementation of the ordered map iterator using generators for iteration.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderedMap extends Generator implements IOrderedMap
{
    use Common\OrderedMap;
    
    public function __construct(\Traversable $values = null)
    {
        parent::__construct();
        
        if($values !== null) {
            foreach($values as $key => $value) {
                $this->set($key, $value);
            }
        }
    }
    
    public function getIterator()
    {
        foreach($this->keys as $position => $key) {
            yield $key => $this->values[$position];
        }
    }
}
