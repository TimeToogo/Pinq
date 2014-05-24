<?php

namespace Pinq\Iterators;

/**
 * Returns the values that satisfy the supplied predicate function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FilterIterator extends IteratorIterator
{
    /**
     * @var callable
     */
    private $filter;

    public function __construct(\Traversable $iterator, callable $filter)
    {
        parent::__construct($iterator);
        $this->filter = Utilities\Functions::allowExcessiveArguments($filter);
    }

    public function valid()
    {
        $filter = $this->filter;

        while (parent::valid()) {
            if ($filter($this->current(), $this->key())) {
                return true;
            }

            parent::next();
        }

        return false;
    }
}
