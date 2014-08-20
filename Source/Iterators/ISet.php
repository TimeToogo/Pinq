<?php

namespace Pinq\Iterators;

/**
 * Interface for an range of unique values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISet extends \Countable, \Traversable
{
    /**
     * Returns whether the values in contained in the set.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function contains($value);

    /**
     * Attempts to add the value to the set, will fail if the value
     * is already contained in the set.
     *
     * @param mixed $value
     *
     * @return boolean Whether the value was successfully added
     */
    public function add($value);

    /**
     * Attempts to add the value by reference to the set, will fail
     * if the value is already contained in the set.
     *
     * @param mixed $value
     *
     * @return boolean Whether the value was successfully added
     */
    public function addRef(&$value);

    /**
     * Removes all values from the set.
     *
     * @return void
     */
    public function clear();

    /**
     * Attempts to remove the value from the set, will fail if the value
     * is not contained in the set.
     *
     * @param mixed $value
     *
     * @return boolean Whether the value was successfully removed
     */
    public function remove($value);
}
