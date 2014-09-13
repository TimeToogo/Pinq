<?php

namespace Pinq\Interfaces;

use Pinq\IQueryable;

/**
 * The API for subsequent orderings of a IQueryable
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOrderedQueryable extends IOrderedTraversable, IQueryable
{
    const IORDERED_QUERYABLE_TYPE = __CLASS__;

    /**
     * {@inheritDoc}
     * @return IOrderedQueryable
     */
    public function thenBy(callable $function, $direction);

    /**
     * {@inheritDoc}
     * @return IOrderedQueryable
     */
    public function thenByAscending(callable $function);

    /**
     * {@inheritDoc}
     * @return IOrderedQueryable
     */
    public function thenByDescending(callable $function);
}
