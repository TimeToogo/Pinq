<?php

namespace Pinq\Iterators;

class FlatteningIterator extends LazyIterator
{
    protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $Array = \Pinq\Utilities::ToArray($InnerIterator);
        
        $FlatArray = [];
        foreach($Array as $Value) {
            if(is_array($Value)) {
                $FlatArray = array_merge($FlatArray, $Value);
            }
            if($Value instanceof \Traversable) {
                $FlatArray = array_merge($FlatArray, \Pinq\Utilities::ToArray($Value));
            }
        }
        
        return new \ArrayIterator($FlatArray);
    }
}
