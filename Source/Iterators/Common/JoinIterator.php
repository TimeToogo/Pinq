<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the join iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait JoinIterator
{
    /**
     * @var callable
     */
    protected $projectionFunction;

    protected function __constructIterator()
    {
        $this->projectionFunction = function ($outerValue) { return $outerValue; };
    }
    
    public function projectTo(callable $function)
    {
        $self = clone $this;
        $self->projectionFunction = Functions::allowExcessiveArguments($function);
        
        return $self;
    }
}
