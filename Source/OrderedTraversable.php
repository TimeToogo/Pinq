<?php

namespace Pinq;

class OrderedTraversable extends Traversable implements \Pinq\IOrderedTraversable
{
    /**
     * @var Iterators\OrderedIterator 
     */
    protected $ValuesIterator;
    
    public function __construct(Iterators\OrderedIterator $OrderedIterator)
    {
        parent::__construct($OrderedIterator);
    }
    
    public function ThenBy(callable $Function, $Direction)
    {
        return new self($this->ValuesIterator->ThenOrderBy($Function, $Direction !== Direction::Descending));
    }
    
    public function ThenByAscending(callable $Function)
    {
        return new self($this->ValuesIterator->ThenOrderBy($Function, true));
    }
    
    public function ThenByDescending(callable $Function)
    {
        return new self($this->ValuesIterator->ThenOrderBy($Function, false));
    }
}
