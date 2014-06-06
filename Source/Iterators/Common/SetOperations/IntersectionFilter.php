<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Returns unique values present in the supplied values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IntersectionFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return $this->set->remove($value);
    }
}
