<?php

namespace Pinq\Interfaces;

use Pinq\IRepository;

/**
 * This API required to combine the filtered joined values into
 * the the elements of the resulting repository.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningToRepository extends IJoiningToQueryable, IJoiningToCollection
{
    const IJOINING_TO_REPOSITORY_TYPE = __CLASS__;

    /**
     * {@inheritDoc}
     * @return IJoiningToRepository
     */
    public function withDefault($value, $key = null);

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
