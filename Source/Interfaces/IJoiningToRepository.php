<?php

namespace Pinq\Interfaces;

use Pinq\IRepository;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting repository.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IJoiningToRepository extends IJoiningToCollection
{
    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function to(callable $joinFunction);
    
    /**
     * {@inheritDoc}
     * @return void
     */
    public function apply(callable $applyFunction);
}
