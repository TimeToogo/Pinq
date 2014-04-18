<?php

namespace Pinq\Iterators;

abstract class OperationIterator extends IteratorIterator
{
    private $OtherIterator;
    
    /**
     * @var Utilities\Set
     */
    private $OtherValues;
    
    public function __construct(\Traversable $Iterator, \Traversable $OtherIterator)
    {
        parent::__construct($Iterator);
        $this->OtherIterator = $OtherIterator;
    }
    
    final public function valid()
    {
        while(parent::valid()) {
            if($this->SetFilter(parent::current(), $this->OtherValues)) {
                return true;
            }
            
            parent::next();
        }
        return false;
    }
    
    protected abstract function SetFilter($Value, Utilities\Set $OtherValues);
    
    final public function rewind()
    {
        $this->OtherValues = new Utilities\Set($this->OtherIterator);
        parent::rewind();
    }
    
}
