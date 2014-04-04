<?php

namespace Pinq;

class GroupedTraversable extends Traversable implements \Pinq\IGroupedTraversable
{
    public function __construct(Iterators\GroupedIterator $GroupedIterator)
    {
        parent::__construct($GroupedIterator);
    }

    public function AndBy(callable $Function)
    {
        return new self($this->ValuesIterator->AndGroupBy($Function));
    }

}
