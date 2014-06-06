<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Removes duplicate values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UniqueFilter extends SetFilter
{
    public function initialize()
    {
        $this->set = $this->scheme->createSet();
    }
    
    public function filter($key, $value)
    {
        return $this->set->add($value);
    }
}
