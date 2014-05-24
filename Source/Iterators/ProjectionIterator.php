<?php

namespace Pinq\Iterators;

/**
 * Returns the values / keys projected by the supplied function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProjectionIterator extends IteratorIterator
{
    /**
     * @var callable|null
     */
    private $keyProjectionFunction;

    /**
     * @var callable|null
     */
    private $valueProjectionFunction;

    public function __construct(\Traversable $iterator, callable $keyProjectionFunction = null, callable $valueProjectionFunction = null)
    {
        parent::__construct($iterator);
        $this->keyProjectionFunction = $keyProjectionFunction === null ? 
                null : Utilities\Functions::allowExcessiveArguments($keyProjectionFunction);
        $this->valueProjectionFunction = $valueProjectionFunction === null ?
                null : Utilities\Functions::allowExcessiveArguments($valueProjectionFunction);
    }

    public function key()
    {
        $function = $this->keyProjectionFunction;

        return $function === null ? parent::key() : $function(parent::current(), parent::key());
    }

    public function current()
    {
        $function = $this->valueProjectionFunction;

        return $function === null ? parent::current() : $function(parent::current(), parent::key());
    }
}
