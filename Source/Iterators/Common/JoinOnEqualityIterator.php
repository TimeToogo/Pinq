<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the join on equality iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait JoinOnEqualityIterator
{
    /**
     * @var callable
     */
    protected $outerKeyFunction;
    
    /**
     * @var callable
     */
    protected $innerKeyFunction;
    
    public function __constructJoinOnEqualityIterator(
            callable $outerKeyFunction, 
            callable $innerKeyFunction)
    {
        $this->outerKeyFunction = Functions::allowExcessiveArguments($outerKeyFunction);
        $this->innerKeyFunction = Functions::allowExcessiveArguments($innerKeyFunction);
    }
    
}
