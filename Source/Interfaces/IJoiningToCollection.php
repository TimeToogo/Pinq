<?php

namespace Pinq\Interfaces;

use Pinq\ICollection;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting ICollection
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningToCollection extends IJoiningToTraversable
{
    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function to(callable $joinFunction);
}
