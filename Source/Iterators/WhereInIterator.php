<?php

namespace Pinq\Iterators;

/**
 * Iterates the values contained in the first values and in the second values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class WhereInIterator extends OperationIterator
{
    protected function setFilter($value, Utilities\Set $otherValues)
    {
        return $otherValues->contains($value);
    }
}
