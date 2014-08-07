<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * Returns unique values present in the comparison values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DifferenceFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return $this->set->add($value);
    }
}
