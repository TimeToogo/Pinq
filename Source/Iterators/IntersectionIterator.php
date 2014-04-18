<?php

namespace Pinq\Iterators;

class IntersectionIterator extends OperationIterator
{
    protected function SetFilter($Value, Utilities\Set $OtherValues)
    {
        return $OtherValues->Remove($Value);
    }
}
