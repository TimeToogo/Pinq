<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Returns values present not present in the comparison values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExceptFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return !$this->set->contains($value);
    }
}
