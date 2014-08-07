<?php

namespace Pinq\Interfaces;

/**
 * This API for the filter options available to a joining IRepository
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningOnRepository extends IJoiningOnCollection, IJoiningToRepository
{
    /**
     * {@inheritDoc}
     * @return IJoiningToRepository
     */
    public function on(callable $function);

    /**
     * {@inheritDoc}
     * @return IJoiningToRepository
     */
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}
