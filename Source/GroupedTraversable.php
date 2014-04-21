<?php

namespace Pinq;

/**
 * The standard implementation for the grouped traversable API, 
 * offering an addition method to specify multiple group bys
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupedTraversable extends Traversable implements IGroupedTraversable
{
    /**
     * @var Iterators\GroupedIterator 
     */
    protected $ValuesIterator;
    
    public function __construct(Iterators\GroupedIterator $GroupedIterator)
    {
        parent::__construct($GroupedIterator);
    }

    public function AndBy(callable $Function)
    {
        return new self($this->ValuesIterator->AndGroupBy($Function));
    }

}
