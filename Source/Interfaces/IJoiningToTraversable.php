<?php

namespace Pinq\Interfaces;

use Pinq\ITraversable;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting ITraversable
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningToTraversable
{
    const IJOINING_TO_TRAVERSABLE_TYPE = __CLASS__;

    /**
     * Sets the default element value and key to be joined if no matching inner
     * elements are found for any outer element.
     *
     * @param mixed $value
     * @param mixed $key
     *
     * @return IJoiningToTraversable
     */
    public function withDefault($value, $key = null);

    /**
     * Returns the appropriate values according to the supplied join function.
     * Both the original and joined values and keys will be passed as arguments
     * to the supplied function as (outerValue, innerValue, outerKey, innerKey).
     *
     * @param callable $joinFunction
     *
     * @return ITraversable
     */
    public function to(callable $joinFunction);
}
