<?php

namespace Pinq\Iterators;

class DifferenceIterator extends OperationIterator
{
    public function valid()
    {
        while(parent::valid()) {
            if($this->OtherValues->Add(parent::current())) {
                return true;
            }
            
            parent::next();
        }
        return false;
    }
}
