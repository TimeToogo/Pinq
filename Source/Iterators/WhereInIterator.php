<?php

namespace Pinq\Iterators;

class WhereInIterator extends OperationIterator
{
    protected function SetFilter($Value, Utilities\Set $OtherValues)
    {
        return $OtherValues->Contains($Value);
    }
}
