<?php

namespace Pinq\Iterators;

class LazyCallbackIterator extends LazyIterator
{
    private $InitializeFunction;
    
    public function __construct(\Traversable $Iterator, callable $InitializeFunction)
    {
        parent::__construct($Iterator);
        $this->InitializeFunction = $InitializeFunction;
    }
    protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $InitializeFunction = $this->InitializeFunction;
        
        return $InitializeFunction($InnerIterator);
    }
}
