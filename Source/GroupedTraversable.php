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
    protected $valuesIterator;

    public function __construct(Iterators\GroupedIterator $groupedIterator)
    {
        parent::__construct($groupedIterator);
    }

    public function andBy(callable $function)
    {
        return new self($this->valuesIterator->andGroupBy($function));
    }
}
