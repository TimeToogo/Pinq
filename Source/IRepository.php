<?php

namespace Pinq;

use Pinq\Interfaces\IJoiningOnRepository;
use Pinq\Interfaces\IOrderedRepository;

/**
 * The repository provides the mutable collection API on a queryable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRepository extends IQueryable, ICollection
{
    const IREPOSITORY_TYPE = __CLASS__;

    /**
     * The repository provider for the implementation.
     *
     * @return Providers\IRepositoryProvider
     */
    public function getProvider();

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function getSource();

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function where(callable $predicate);

    /**
     * {@inheritDoc}
     * @return IOrderedRepository
     */
    public function orderBy(callable $function, $direction);

    /**
     * {@inheritDoc}
     * @return IOrderedRepository
     */
    public function orderByAscending(callable $function);

    /**
     * {@inheritDoc}
     * @return IOrderedRepository
     */
    public function orderByDescending(callable $function);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function skip($amount);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function take($amount);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function slice($start, $amount);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function indexBy(callable $function);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function keys();

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function reindex();

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function groupBy(callable $function);

    /**
     * {@inheritDoc}
     * @return IJoiningOnRepository
     */
    public function join($values);

    /**
     * {@inheritDoc}
     * @return IJoiningOnRepository
     */
    public function groupJoin($values);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function unique();

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function select(callable $function);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function selectMany(callable $function);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function append($values);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function whereIn($values);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function except($values);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function union($values);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function intersect($values);

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    public function difference($values);
}
