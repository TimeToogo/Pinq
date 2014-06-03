<?php

namespace Pinq\Iterators;

/**
 * Iterates the unique values contained in the first values and in the second values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IntersectionIterator extends OperationIterator
{
    protected function setFilter($key, $value, Utilities\Set $otherValues)
    {
        return $otherValues->remove($value);
    }
}
