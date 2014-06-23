<?php

namespace Pinq\Interfaces;

use Pinq\ITraversable;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting ITraversable
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningToTraversable
{
    /**
     * Returns the appropriate values according to the supplied join function.
     * Both the original and joined values and keys will be passed as arguments 
     * to the supplied function as (outerValue, innerValue, outerKey, innerKey).
     *
     * @param callable $joinFunction
     * @return ITraversable
     */
    public function to(callable $joinFunction);
}
