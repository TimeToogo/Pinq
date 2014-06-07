<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Removes duplicate keys
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UniqueKeyFilter extends UniqueFilter
{
    public function filter($key, $value)
    {
        return $this->set->add($key);
    }
}
