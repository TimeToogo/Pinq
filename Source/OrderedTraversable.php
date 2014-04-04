<?php

namespace Pinq;

class OrderedTraversable extends Traversable implements \Pinq\IOrderedTraversable
{
    public function __construct(Iterators\OrderedIterator $OrderedIterator)
    {
        parent::__construct($OrderedIterator);
    }
    
    public function ThenBy(callable $Function)
    {
        return new self($this->ValuesIterator->ThenOrderBy($Function, true));
    }
    
    public function ThenByDescending(callable $Function)
    {
        return new self($this->ValuesIterator->ThenOrderBy($Function, false));
    }
}
