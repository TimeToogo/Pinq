<?php

namespace Pinq;

use Pinq\Interfaces\IJoiningOnCollection;
use Pinq\Interfaces\IOrderedCollection;

/**
 * The collection API, along with traversable query API,
 * a collection's values are mutable, they can be added, removed and altered.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ICollection extends ITraversable
{
    const ICOLLECTION_TYPE = __CLASS__;

    /**
     * Walks the elements with the supplied function.
     *
     * @param callable $function
     *
     * @return void
     */
    public function apply(callable $function);

    /**
     * Adds a range of values to the collection.
     * The collection will be reindexed.
     *
     * @param array|\Traversable $values The values to add
     *
     * @return void
     */
    public function addRange($values);

    /**
     * Removes all occurrences of the supplied value from the collection.
     *
     * @param mixed $value The value to remove
     *
     * @return void
     */
    public function remove($value);

    /**
     * Removes all occurrences of the supplied values from the collection.
     *
     * @param array|\Traversable $values The values to remove
     *
     * @return void
     */
    public function removeRange($values);

    /**
     * Removes all the values matched by the predicate.
     *
     * @param callable $predicate
     *
     * @return void
     */
    public function removeWhere(callable $predicate);

    /**
     * Removes all the values.
     *
     * @return void
     */
    public function clear();

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function getSource();

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function where(callable $predicate);

    /**
     * {@inheritDoc}
     * @return IOrderedCollection
     */
    public function orderBy(callable $function, $direction);

    /**
     * {@inheritDoc}
     * @return IOrderedCollection
     */
    public function orderByAscending(callable $function);

    /**
     * {@inheritDoc}
     * @return IOrderedCollection
     */
    public function orderByDescending(callable $function);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function skip($amount);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function take($amount);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function slice($start, $amount);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function indexBy(callable $function);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function keys();

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function reindex();

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function groupBy(callable $function);

    /**
     * {@inheritDoc}
     * @return IJoiningOnCollection
     */
    public function join($values);

    /**
     * {@inheritDoc}
     * @return IJoiningOnCollection
     */
    public function groupJoin($values);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function unique();

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function select(callable $function);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function selectMany(callable $function);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function append($values);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function whereIn($values);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function except($values);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function union($values);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function intersect($values);

    /**
     * {@inheritDoc}
     * @return ICollection
     */
    public function difference($values);
}
