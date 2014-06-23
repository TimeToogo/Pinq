<?php

namespace Pinq\Iterators;


/**
 * Interface for an ordered iterator that supports subsequent ordering.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoinToIterator extends IWrapperIterator
{
    /**
     * Returns a new join iterator that will project the values with the supplied
     * function, called with the parameters ($outerValue, $innerValue, $outerKey, $innerKey).
     * 
     * @param callable $function
     * @return \Traversable
     */
    public function projectTo(callable $function);
    
    /**
     * Walks the joined elements
     * 
     * @param callable $function
     * @return void
     */
    public function walk(callable $function);
}
