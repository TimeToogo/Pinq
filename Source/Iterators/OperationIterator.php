<?php

namespace Pinq\Iterators;

abstract class OperationIterator extends LazyIterator
{
    private $OtherIterator;
    
    /**
     * @var Utilities\Set
     */
    protected $OtherValues;
    
    public function __construct(\Traversable $Iterator, \Traversable $OtherIterator)
    {
        parent::__construct($Iterator);
        $this->OtherIterator = $OtherIterator;
    }
    
    final protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $this->OtherValues = new Utilities\Set($this->OtherIterator);
    }
}
