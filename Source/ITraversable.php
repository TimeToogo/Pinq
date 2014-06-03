<?php

namespace Pinq;

use Pinq\Interfaces\IJoiningOnTraversable;
use Pinq\Interfaces\IOrderedTraversable;

/**
 * The root interface providing a fluent query API for a range of values.
 *
 * Query calls must be immutable and return a new instance with every query call.
 * Queries should also be executed lazily upon iteration.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ITraversable extends IAggregatable, \IteratorAggregate, \ArrayAccess
{
    const ITRAVERSABLE_TYPE = __CLASS__;

    /**
     * Returns the values as an array.
     * Only valid array scalar keys (or null) will be used, 
     * all others will be reindexed numerically.
     *
     * @return array
     */
    public function asArray();

    /**
     * Returns the values as a traversable.
     * The following queries will be performed in memory.
     *
     * @return ITraversable
     */
    public function asTraversable();

    /**
     * Returns the values as a collection.
     * The following queries will be performed in memory.
     *
     * @return ICollection
     */
    public function asCollection();

    /**
     * Iterates the elements with the supplied function.
     * Returning false will break the iteration loop
     *
     * @param callable $function The iteration function, parameters are passed as ($value, $key)
     * @return void
     */
    public function iterate(callable $function);

    /**
     * Returns an array compatable iterator for the elements.
     * Non scalar keys will be numerically reindexed.
     *
     * @return \Iterator
     */
    public function getIterator();
    
    /**
     * Returns an iterator for all the elements.
     * All keys types will remain unaltered.
     *
     * @return \Iterator
     */
    public function getTrueIterator();

    /**
     * Returns the first value, null if empty
     *
     * @return mixed The first value
     */
    public function first();

    /**
     * Returns the last value, null if empty
     *
     * @return mixed The last value
     */
    public function last();

    /**
     * Returns whether the supplied value is contained within the aggregate
     *
     * @param mixed $value The value to check for
     * @return boolean
     */
    public function contains($value);

    /**
     * Filters the values by a supplied predicate.
     *
     * @param  callable   $predicate The predicate function
     * @return ITraversable
     */
    public function where(callable $predicate);

    /**
     * Orders the values mapped from the supplied function according the supplied
     * direction.
     *
     * @param  callable          $function The projection function
     * @param  int               $direction
     * @return IOrderedTraversable
     */
    public function orderBy(callable $function, $direction);

    /**
     * Orders the values mapped from the supplied function ascendingly
     *
     * Example function:
     *
     * @param  callable          $function The mapping function
     * @return IOrderedTraversable
     */
    public function orderByAscending(callable $function);

    /**
     * Orders the values mapped from the supplied function descendingly
     *
     * Example expression function:
     *
     * @param  callable          $function The mapping function
     * @return IOrderedTraversable
     */
    public function orderByDescending(callable $function);

    /**
     * Skip the amount of values from the start.
     *
     * @param  int        $amount The amount of values to skip, must be > 0
     * @return ITraversable
     */
    public function skip($amount);

    /**
     * Limits the amount of values by the supplied amount
     *
     * @param  int|null   $amount The amount of values to retrieve, must be > 0 or null if all
     * @return ITraversable
     */
    public function take($amount);

    /**
     * Retrieve a slice of the values.
     *
     * @param  int        $start  The amount of values to skip
     * @param  int|null   $amount The amount of values to retrieve
     * @return ITraversable
     */
    public function slice($start, $amount);

    /**
     * Index the values according to the supplied mapping function.
     * All duplicate indexes will return the first associated value.
     *
     * @param  callable   $function The projection function
     * @return ITraversable
     */
    public function indexBy(callable $function);

    /**
     * Selects the keys (numerically indexed by their 0-based position).
     *
     * @return ITraversable
     */
    public function keys();

    /**
     * Reindexes the values (numerically reindexed by their 0-based position).
     *
     * @return ITraversable
     */
    public function reindex();

    /**
     * Groups values according the supplied function. (Uses strict equality '===')
     * The values will be grouped into instances of the traversable.
     * This implicitly index each group by the group key returned from the supplied function.
     *
     * @param  callable   $function The grouping function
     * @return ITraversable
     */
    public function groupBy(callable $function);

    /**
     * Matches the original values with the supplied values according to the supplied function.
     *
     * @param  array|\Traversable   $values
     * @return IJoiningOnTraversable
     */
    public function join($values);

    /**
     * Matches the original values with the supplied values according to the supplied functions
     * then groups the values.
     *
     * @param  array|\Traversable   $values
     * @return IJoiningOnTraversable
     */
    public function groupJoin($values);

    /**
     * Only return unique values. (Uses strict equality '===')
     *
     * @return ITraversable
     */
    public function unique();

    /**
     * Returns the values mapped by the supplied function.
     *
     * @param  callable   $function The function returning the data to select
     * @return ITraversable
     */
    public function select(callable $function);

    /**
     * Returns the values mapped by the supplied function and then flattens
     * the values into a single traversable. Keys will be reindexed.
     *
     * @param  callable   $function The function returning the data to select
     * @return ITraversable
     */
    public function selectMany(callable $function);

    /**
     * Returns values from the original and supplied values, keys will be reindexed.
     *
     * @param  array|\Traversable $values The values to append
     * @return ITraversable
     */
    public function append($values);

    /**
     * Returns all values from the original present in the supplied values.
     * (Uses strict equality '===')
     *
     * @param  array|\Traversable $values
     * @return ITraversable
     */
    public function whereIn($values);

    /**
     * Returns values all values from the original not present in the supplied values.
     * (Uses strict equality '===')
     *
     * @param  array|\Traversable $values The values to union
     * @return ITraversable
     */
    public function except($values);

    /**
     * Returns unique results present in both the original and supplied values,
     * keys will be reindexed.
     * (Uses strict equality '===')
     *
     * @param  array|\Traversable $values The values to union
     * @return ITraversable
     */
    public function union($values);

    /**
     * Returns unique values the are present in the original and supplied values.
     * (Uses strict equality '===')
     *
     * @param  array|\Traversable $values The values to intersect with
     * @return ITraversable
     */
    public function intersect($values);

    /**
     * Removes unique values from the original not present in the supplied values.
     * (Uses strict equality '===')
     *
     * @param  array|\Traversable $values The values to remove
     * @return ITraversable
     */
    public function difference($values);
}
