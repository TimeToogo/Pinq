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
    
    public function __construct(IIterator $values = null)
    {
        parent::__construct();
        
        if($values !== null) {
            $values->rewind();
            while ($values->fetch($key, $value)) {
                $this->add($value);
            }
        }
    }
    
    protected function doRewind()
    {
        reset($this->values);
    }
    
    protected function doFetch(&$key, &$value)
    {
        return false !== (list($key, $value) = each($this->values));
    }
}
