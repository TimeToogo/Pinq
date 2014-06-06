<?php

namespace Pinq\Interfaces;

use Pinq\IRepository;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting ICollection
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningToRepository extends IJoiningToTraversable
{
    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function to(callable $joinFunction);
}
