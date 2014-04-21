<?php

namespace Pinq;

/**
 * Implements the subsequent ordering API for the traversable query.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderedTraversable extends Traversable implements IOrderedTraversable
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
