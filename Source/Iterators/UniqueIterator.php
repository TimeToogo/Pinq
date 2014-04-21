<?php

namespace Pinq\Iterators;

/**
 * Iterates the unique values in the orignal values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UniqueIterator extends OperationIterator
{    
    public function __construct(\Traversable $Iterator)
    {
        parent::__construct($Iterator, new \ArrayIterator());
    }
    
    protected function SetFilter($Value, Utilities\Set $SeenValues)
    {
        return $SeenValues->Add($Value);
    }
}
