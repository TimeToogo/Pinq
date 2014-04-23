<?php

namespace Pinq;

/**
 * The collection API, along with traversable query API,
 * a collection's values are mutable, they can be added, removed and altered.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ICollection extends ITraversable
{
    /**
     * Applies the function the list of values
     *
     * @param  callable $function
     * @return void
     */
    public function apply(callable $function);

    /**
     * Adds a range of values to the collection.
     * The collection will be reindexed.
     *
     * @param  array|\Traversable $values The values to add
     * @return void
     */
    public function addRange($values);

    /**
     * Removes a range of values from the collection
     *
     * @param  array|\Traversable $values The values to remove
     * @return void
     */
    public function removeRange($values);

    /**
     * Removes all the values matched by the predicate
     *
     * @param  callable $predicate
     * @return void
     */
    public function removeWhere(callable $predicate);

    /**
     * Removes all the values.
     *
     * @return void
     */
    public function clear();
}
