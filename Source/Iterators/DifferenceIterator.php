<?php

namespace Pinq\Iterators;

/**
 * Iterates the unique values contained in the first values but not in the second values.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DifferenceIterator extends OperationIterator
{
    protected function SetFilter($Value, Utilities\Set $OtherValues)
    {
        return $OtherValues->Add($Value);
    }
}
