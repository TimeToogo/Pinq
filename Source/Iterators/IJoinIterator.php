<?php

namespace Pinq\Iterators;


/**
 * Interface for an ordered iterator that supports subsequent ordering.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoinIterator extends IJoinToIterator
{
    /**
     * 
     * 
     * @param callable $function
     * @return IJoinToIterator
     */
    public function filterOn(callable $function);
    
    /**
     * 
     * 
     * @param callable $function
     * @return IJoinToIterator
     */
    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}
