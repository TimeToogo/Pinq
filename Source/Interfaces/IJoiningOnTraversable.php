<?php

namespace Pinq\Interfaces;

/**
 * This API for the filter options available to a joining ITraversable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningOnTraversable extends IJoiningToTraversable
{
    const IJOINING_ON_TRAVERSABLE_TYPE = __CLASS__;

    /**
     * Filters the join values according to the supplied function.
     * Both values and keys will be passed as arguments to the supplied function
     * as (outerValue, innerValue, outerKey, innerKey).
     *
     * @param callable $function
     *
     * @return IJoiningToTraversable
     */
    public function on(callable $function);

    /**
     * Filters the join values via strict equality (===) between the outer and inner keys
     * according to the supplied key functions. Note that null keys are ignored.
     *
     * @param callable $outerKeyFunction
     * @param callable $innerKeyFunction
     *
     * @return IJoiningToTraversable
     */
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}
