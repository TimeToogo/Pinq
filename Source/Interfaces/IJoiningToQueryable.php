<?php

namespace Pinq\Interfaces;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting ICollection
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningToQueryable extends IJoiningToTraversable
{
    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function to(callable $joinFunction);
}
