<?php

namespace Pinq;

/**
 * A mutable list of values.
 */
interface ICollection extends ITraversable, \ArrayAccess
{
    /**
     * Applies the function the list of values
     *
     * @param  callable $Function
     * @return void
     */
    public function Apply(callable $Function);

    /**
     * Adds a range of values to the collection
     *
     * @param  array|\Traversable $Values The values to add
     * @return void
     */
    public function AddRange($Values);

    /**
     * Removes a range of values from the collection
     *
     * @param  array|\Traversable $Values The values to remove
     * @return void
     */
    public function RemoveRange($Values);

    /**
     * Removes all the values matched by the predicate
     *
     * @param  callable $Predicate
     * @return void
     */
    public function RemoveWhere(callable $Predicate);

    /**
     * Removes all the values.
     *
     * @param  callable $Function
     * @return void
     */
    public function Clear();
}
