<?php

namespace Pinq\Iterators;

class ExceptIterator extends OperationIterator
{
    public function valid()
    {
        while(parent::valid()) {
            $CurrentValue = self::current();
            
            if(!in_array($CurrentValue, $this->OtherValues, true)) {
                return true;
            }
            
            parent::next();
        }
        return false;
    }
}
