<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Returns unique values present in the comparison values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DifferenceFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return $this->set->add($value);
    }
}
