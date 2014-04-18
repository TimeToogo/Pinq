<?php

namespace Pinq\Iterators;

class DifferenceIterator extends OperationIterator
{
    protected function SetFilter($Value, Utilities\Set $OtherValues)
    {
        return $OtherValues->Add($Value);
    }
}
