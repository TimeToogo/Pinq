<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Returns values prensent in the comparison values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class WhereInFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return $this->set->contains($value);
    }
}
