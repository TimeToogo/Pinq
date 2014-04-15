<?php

namespace Pinq;

/**
 * The root interface providing a fluent query API for a set of values.
 * 
 * Query calls must be immutable and return a new instance with every query call.
 * Queries should also be executed lazily upon iteration.
 * 
 */
interface ITraversable extends IAggregatable, \IteratorAggregate, \ArrayAccess
{
    const ITraversableType = __CLASS__;
    
    /**
     * Returns the values as an array
     *
     * @return array
     */
    public function AsArray();

    /**
     * Returns the values as a traversable.
     * If the implementation is queryable the following operations will
     * be performed in memory.
     *
     * @return ITraversable
     */
    public function AsTraversable();

    /**
     * Returns the values as a collection.
     * If the implementation is queryable the following operations will
     * be performed in memory.
     *
     * @return ICollection
     */
    public function AsCollection();

    /**
     * Returns the values as a queryable.
     *
     * @return IQueryable
     */
    public function AsQueryable();

    /**
     * Returns the values as a repository.
     *
     * @return IQueryable
     */
    public function AsRepository();
    
    /**
     * Returns the first value, null if empty
     * 
     * @return mixed The first value 
     */
    public function First();
    
    /**
     * Returns the last value, null if empty
     * 
     * @return mixed The last value 
     */
    public function Last();
    
    /**
     * Returns whether the supplied value is contained within the aggregate
     * 
     * @param mixed $Value The value to check for
     * @return boolean
     */
    public function Contains($Value);
    
    /**
     * Filters the values by a supplied predicate.
     *
     * @param  callable   $Predicate The predicate function
     * @return ITraversable
     */
    public function Where(callable $Predicate);

    /**
     * Orders the values mapped from the supplied function according the supplied
     * direction.
     *
     * @param  callable          $Function The projection function
     * @param  int               $Direction
     * @return IOrderedTraversable
     */
    public function OrderBy(callable $Function, $Direction);

    /**
     * Orders the values mapped from the supplied function ascendingly
     *
     * Example function:
     *
     * @param  callable          $Function The mapping function
     * @return IOrderedTraversable
     */
    public function OrderByAscending(callable $Function);

    /**
     * Orders the values mapped from the supplied function descendingly
     *
     * Example expression function:
     *
     * @param  callable          $Function The mapping function
     * @return IOrderedTraversable
     */
    public function OrderByDescending(callable $Function);

    /**
     * Skip the amount of values from the start.
     *
     * @param  int        $Amount The amount of values to skip, must be > 0
     * @return ITraversable
     */
    public function Skip($Amount);

    /**
     * Limits the amount of values by the supplied amount
     *
     * @param  int|null   $Amount The amount of values to retrieve, must be > 0 or null if all
     * @return ITraversable
     */
    public function Take($Amount);

    /**
     * Retrieve a slice of the values
     *
     * @param  int        $Start  The amount of values to skip
     * @param  int|null   $Amount The amount of values to retrieve
     * @return ITraversable
     */
    public function Slice($Start, $Amount);

    /**
     * Index the values according to the supplied mapping function.
     *
     * @param  callable   $Function The projection function
     * @return ITraversable
     */
    public function IndexBy(callable $Function);

    /**
     * Groups values according the supplied function. (Uses strict equality '===')
     * The values will be grouped into instances of ITraversable.
     *
     * @param  callable   $Function The grouping function
     * @return ITraversable
     */
    public function GroupBy(callable $Function);

    /**
     * Only return unique values. (Uses strict equality '===')
     *
     * @return ITraversable
     */
    public function Unique();

    /**
     * Returns the values mapped by the supplied function.
     *
     * @param  callable   $Function The function returning the data to select
     * @return ITraversable
     */
    public function Select(callable $Function);

    /**
     * Returns the values mapped by the supplied function and then flattens 
     * the values into a single traversable. Keys will be reindexed.
     *
     * @param  callable   $Function The function returning the data to select
     * @return ITraversable
     */
    public function SelectMany(callable $Function);

    /**
     * Returns values from the original and supplied values, keys will be reindexed.
     *
     * @param  ITraversable $Values The values to append
     * @return ITraversable
     */
    public function Append($Values);

    /**
     * Returns all values from the original present in the supplied values. 
     * (Uses strict equality '===') 
     *
     * @param  ITraversable $Values
     * @return ITraversable
     */
    public function WhereIn($Values);

    /**
     * Returns values all values from the original not present in the supplied values.
     * (Uses strict equality '===') 
     *
     * @param  ITraversable $Values The values to union
     * @return ITraversable
     */
    public function Except($Values);

    /**
     * Returns unique results present in both the original and supplied values, 
     * keys will be reindexed.
     * (Uses strict equality '===') 
     *
     * @param  ITraversable $Values The values to union
     * @return ITraversable
     */
    public function Union($Values);

    /**
     * Returns unique values the are present in the original and supplied values. 
     * (Uses strict equality '===') 
     *
     * @param  ITraversable $Values The values to intersect with
     * @return ITraversable
     */
    public function Intersect($Values);

    /**
     * Removes unique values from the original not present in the supplied values. 
     * (Uses strict equality '===') 
     *
     * @param  ITraversable $Values The values to remove
     * @return ITraversable
     */
    public function Difference($Values);
}
