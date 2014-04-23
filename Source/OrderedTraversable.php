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
    protected $valuesIterator;
    
    public function __construct(Iterators\OrderedIterator $orderedIterator)
    {
        parent::__construct($orderedIterator);
    }
    
    public function thenBy(callable $function, $direction)
    {
        return new self($this->valuesIterator->thenOrderBy(
                $function,
                $direction !== Direction::DESCENDING));
    }
    
    public function thenByAscending(callable $function)
    {
        return new self($this->valuesIterator->thenOrderBy($function, true));
    }
    
    public function thenByDescending(callable $function)
    {
        return new self($this->valuesIterator->thenOrderBy($function, false));
    }
}