<?php

namespace Pinq\Iterators;

class ExceptIterator extends OperationIterator
{
    protected function SetFilter($Value, Utilities\Set $OtherValues)
    {
        return !$OtherValues->Contains($Value);
    }
}
