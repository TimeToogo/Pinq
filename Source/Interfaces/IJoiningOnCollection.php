<?php

namespace Pinq\Interfaces;

/**
 * This API for the filter options available to a joining ITraversable
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningOnCollection extends IJoiningOnTraversable, IJoiningToCollection
{
    const IJOINING_ON_COLLECTION_TYPE = __CLASS__;

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
