<?php

namespace Pinq\Iterators;

class FlatteningIterator extends LazyIterator
{
    protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $Array = \Pinq\Utilities::ToArray($InnerIterator);
        
        $ReturnedArrays = [];
        foreach($Array as $Value) {
            if(is_array($Value)) {
                $ReturnedArrays[] = array_values($Value);
            }
            if($Value instanceof \Traversable) {
                $ReturnedArrays[] = array_values(\Pinq\Utilities::ToArray($Value));
            }
        }
        $FlatArray = empty($ReturnedArrays) ? [] : call_user_func_array('array_merge', $ReturnedArrays);
        
        return new \ArrayIterator($FlatArray);
    }
}
