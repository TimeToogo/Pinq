<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * Returns values present in the comparison values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class WhereInFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return $this->set->contains($value);
    }
}
