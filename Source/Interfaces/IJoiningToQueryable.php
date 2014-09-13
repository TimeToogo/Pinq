<?php

namespace Pinq\Interfaces;

use Pinq\IQueryable;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting ICollection
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningToQueryable extends IJoiningToTraversable
{
    const IJOINING_TO_QUERYABLE_TYPE = __CLASS__;

    /**
     * {@inheritDoc}
     * @return IJoiningToQueryable
     */
    public function withDefault($value, $key = null);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function to(callable $joinFunction);
}
