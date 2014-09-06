<?php

namespace Pinq;

use Pinq\Interfaces\IJoiningOnQueryable;
use Pinq\Interfaces\IOrderedQueryable;

/**
 * The queryable provides the traversable query API, on an abstracted data source from the query provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryable extends ITraversable
{
    const IQUERYABLE_TYPE = __CLASS__;

    /**
     * The query provider for the implementation.
     *
     * @return Providers\IQueryProvider
     */
    public function getProvider();

    /**
     * Gets the expression representing the current query scope.
     *
     * @return Expressions\Expression
     */
    public function getExpression();

    /**
     * Gets the source info.
     *
     * @return Queries\ISourceInfo
     */
    public function getSourceInfo();

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function getSource();

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function where(callable $predicate);

    /**
     * {@inheritDoc}
     * @return IOrderedQueryable
     */
    public function orderBy(callable $function, $direction);

    /**
     * {@inheritDoc}
     * @return IOrderedQueryable
     */
    public function orderByAscending(callable $function);

    /**
     * {@inheritDoc}
     * @return IOrderedQueryable
     */
    public function orderByDescending(callable $function);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function skip($amount);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function take($amount);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function slice($start, $amount);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function indexBy(callable $function);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function keys();

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function reindex();

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function groupBy(callable $function);

    /**
     * {@inheritDoc}
     * @return IJoiningOnQueryable
     */
    public function join($values);

    /**
     * {@inheritDoc}
     * @return IJoiningOnQueryable
     */
    public function groupJoin($values);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function unique();

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function select(callable $function);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function selectMany(callable $function);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function append($values);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function whereIn($values);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function except($values);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function union($values);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function intersect($values);

    /**
     * {@inheritDoc}
     * @return IQueryable
     */
    public function difference($values);
}
