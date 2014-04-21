<?php

namespace Pinq;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting traversable
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningToTraversable
{    
    /**
     * Returns the appropriate values according to the supplied join function.
     * Both values will be passed as arguments to the supplied function.
     * 
     * @param callable $JoinFunction 
     * @return ITraversable
     */
    public function To(callable $JoinFunction);
}
