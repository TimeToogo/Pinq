<?php 

namespace Pinq;

/**
 * This API for the filter options available to a joining traversable
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningOnTraversable
{
    /**
     * Filters the join values according to the supplied function.
     * Both values will be passed as arguments to the supplied function.
     *
     * @param  callable $function
     * @return IJoiningToTraversable
     */
    public function on(callable $function);
    
    /**
     * Filters the join values via strict equality (===) on the outer and inner keys
     * according to the supplied key functions.
     *
     * @param  callable $outerKeyFunction
     * @param  callable $innerKeyFunction
     * @return IJoiningToTraversable
     */
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}