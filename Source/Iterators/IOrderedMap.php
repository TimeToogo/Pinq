<?php

namespace Pinq\Iterators;

/**
 * Interface for an ordered map, like an array on steroids,
 * this class supports any type of key associated with any
 * type of value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOrderedMap extends \Traversable, \ArrayAccess, \Countable
{
    /**
     * Returns all the keys from the map as an array.
     * Indexed by their 0-based position.
     *
     * @return array
     */
    public function keys();

    /**
     * Returns all the values from the map as an array.
     * Indexed by their 0-based position.
     *
     * @return array
     */
    public function values();

    /**
     * Maps the keys / values of the dictionary to new dictionary.
     *
     * @param callable $function The elements are passed as ($value, $key)
     *
     * @return IOrderedMap
     */
    public function map(callable $function);

    /**
     * Walks the elements with the supplied function
     *
     * @param callable $function
     *
     * @return void
     */
    public function walk(callable $function);

    /**
     * Groups the keys / values using the supplied function
     * into new ordered map.
     *
     * @param callable $groupKeyFunction
     *
     * @return IOrderedMap
     */
    public function groupBy(callable $groupKeyFunction);

    /**
     * Creates a new ordered map with the keys and values
     * sorted according the the supplied functions and order
     * directions.
     *
     * @param callable[] $orderFunctions
     * @param boolean[]  $isAscending
     *
     * @return IOrderedMap
     */
    public function multisort(array $orderFunctions, array $isAscending);

    /**
     * Returns the value associated with the supplied key or null
     * if it does not exist.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function &get($key);

    /**
     * Returns the value associated with the supplied key or null
     * if it does not exist.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function &offsetGet($key);

    /**
     * Returns whether their is value associated with the supplied key.
     *
     * @param mixed $key
     *
     * @return boolean
     */
    public function contains($key);

    /**
     * Sets the supplied key to the supplied value.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Sets the supplied key to the supplied value by reference.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function setRef($key, &$value);

    /**
     * Sets the supplied keys and values from the elements iterator.
     *
     * @param \Traversable $elements
     *
     * @return void
     */
    public function setAll(\Traversable $elements);

    /**
     * Removes all keys and values from the map.
     *
     * @return void
     */
    public function clear();

    /**
     * Removes the element (if exists) with the supplied key.
     *
     * @param mixed $key
     *
     * @return boolean Whether the key was succefully remove or false if does not exist
     */
    public function remove($key);

}
