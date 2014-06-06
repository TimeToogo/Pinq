<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\ISet;
use Pinq\Iterators\Common;

/**
 * Implementation of the set using the fetch method for iteration.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Set extends Iterator implements ISet
{
    use Common\Set;
    
    /**
     * @var int 
     */
    private $position = 0;
    
    public function __construct(IIterator $values = null)
    {
        parent::__construct();
        
        $this->map = new OrderedMap();
        
        if($values !== null) {
            $values->rewind();
            while ($values->fetch($key, $value)) {
                $this->add($value);
            }
        }
    }
    
    protected function doRewind()
    {
        $this->position = 0;
        $this->map->rewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        $key = $this->position++;
        return $this->map->fetch($value, $null);
    }
}
