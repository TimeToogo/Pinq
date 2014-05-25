<?php

namespace Pinq\Interfaces;

/**
 * This API for the filter options available to a joining ITraversable
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningOnCollection extends IJoiningOnTraversable
{
    /**
     * {@inheritDoc}
     * @return IJoiningToCollection
     */
    public function on(callable $function);

    /**
     * {@inheritDoc}
     * @return IJoiningToCollection
     */
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}
