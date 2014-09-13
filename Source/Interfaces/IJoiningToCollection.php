<?php

namespace Pinq\Interfaces;

use Pinq\ICollection;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningToCollection extends IJoiningToTraversable
{
    const IJOINING_TO_COLLECTION_TYPE = __CLASS__;

    /**
     * {@inheritDoc}
     * @return IJoiningToCollection
     */
    public function withDefault($value, $key = null);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function to(callable $joinFunction);

    /**
     * Walks the elements with the supplied function.
     * Both the original and joined values and keys will be passed as arguments
     * to the supplied function as (&outerValue, innerValue, outerKey, innerKey).
     *
     * @param callable $applyFunction Called with parameters (&outerValue, innerValue, outerKey, innerKey)
     *
     * @return void
     */
    public function apply(callable $applyFunction);
}
