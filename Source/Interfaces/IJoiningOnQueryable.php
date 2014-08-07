<?php

namespace Pinq\Interfaces;

/**
 * This API for the filter options available to a joining IQueryable
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoiningOnQueryable extends IJoiningOnTraversable, IJoiningToQueryable
{
    /**
     * {@inheritDoc}
     * @return IJoiningToQueryable
     */
    public function on(callable $function);

    /**
     * {@inheritDoc}
     * @return IJoiningToQueryable
     */
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}
