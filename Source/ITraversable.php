<?php

namespace Pinq;

/**
 * The root interface providing a fluent query API for a set of values.
 * Implementing classes must be immutable and return a new instance with every query call.
 * 
 */
interface ITraversable extends IAggregatable, \IteratorAggregate
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
     * Filters the values by a predicate function.
     *
     * @param  callable   $Predicate The predicate function
     * @return ITraversable
     */
    public function Where(callable $Predicate);

    /**
     * Orders the values mapped from the supplied function according the the supplied
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
     * Retrieve only the specified amount of values
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
     * Return only unique values. (Uses strict equality '===')
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
     * the arrays or iteratorable values. Keys will be ignored.
     *
     * @param  callable   $Function The function returning the data to select
     * @return ITraversable
     */
    public function SelectMany(callable $Function);

    /**
     * Returns the two results merged. Any duplicate keys or values will remain as the
     * original value.
     *
     * @param  ITraversable $Values The values to union
     * @return ITraversable
     */
    public function Union(ITraversable $Values);

    /**
     * Returns the two results merged. Keys will be ignored.
     *
     * @param  ITraversable $Values The values to append
     * @return ITraversable
     */
    public function Append(ITraversable $Values);

    /**
     * Returns the intersection of the results. (Uses strict equality '===') 
     *
     * @param  ITraversable $Values The values to intersect with
     * @return ITraversable
     */
    public function Intersect(ITraversable $Values);

    /**
     * Removes any values present in the supplied traversable. (Uses strict equality '===') 
     *
     * @param  ITraversable $Values The values to remove
     * @return ITraversable
     */
    public function Except(ITraversable $Values);
}
