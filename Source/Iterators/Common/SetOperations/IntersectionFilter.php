<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * Returns unique values present in the supplied values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IntersectionFilter extends ComparisonFilter
{
    public function filter($key, $value)
    {
        return $this->set->remove($value);
    }
}
