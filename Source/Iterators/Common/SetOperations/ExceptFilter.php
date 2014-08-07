<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * Returns values present not present in the comparison values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExceptFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return !$this->set->contains($value);
    }
}
