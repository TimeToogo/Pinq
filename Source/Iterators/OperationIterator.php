<?php

namespace Pinq\Iterators;

abstract class OperationIterator extends LazyIterator
{
    private $OtherIterator;
    
    protected $OtherValues;
    
    public function __construct(\Traversable $Iterator, \Traversable $OtherIterator)
    {
        parent::__construct($Iterator);
        $this->OtherIterator = $OtherIterator;
    }
    
    final protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $this->OtherValues = \Pinq\Utilities::ToArray($this->OtherIterator);
    }
}
