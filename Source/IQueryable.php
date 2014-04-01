<?php

namespace Pinq;

/**
 *
 */
interface IQueryable extends IConvertable//, ITraversable
{
    /**
     * Gets the query provider of the queryable implementation
     *
     * @return IQueryProvider
     */
    public function GetProvider();

    /**
     * Specifies a predicate
     *
     * Example predicate function:
     * <code>
     *  function (Car $Car) use ($Name) {
     *      return $Car->IsAvailable() && $Car->GetName() === $Name;
     *  }
     * </code>
     *
     * @param  callable   $Predicate The predicate function
     * @return IQueryable
     */
    public function Where(callable $Predicate);

    /**
     * Specifies the function to use for ascending ordering
     *
     * Example expression function:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     *
     * @param  callable          $Function The expression function
     * @return IOrderedQueryable
     */
    public function OrderBy(callable $Function);

    /**
     * Specifies the function to use for descending ordering.
     *
     * Example expression function:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     *
     * @param  callable          $Function The expression function
     * @return IOrderedQueryable
     */
    public function OrderByDescending(callable $Function);

    /**
     * Specifies the amount to skip.
     *
     * @param  int        $Amount The amount of values to skip
     * @return IQueryable
     */
    public function Skip($Amount);

    /**
     * Specifies the amount to retrieve. Pass null to remove the limit.
     *
     * @param  int|null   $Amount The amount of values to retrieve
     * @return IQueryable
     */
    public function Take($Amount);

    /**
     * Returns a slice of the collection
     *
     * @param  int        $Start  The amount of values to skip
     * @param  int|null   $Amount The amount of values to retrieve
     * @return IQueryable
     */
    public function Slice($Start, $Amount);

    /**
     * Will use the values of the supplied function as the index
     *
     * @param  callable   $Function The function returning the key data
     * @return IQueryable
     */
    public function IndexBy(callable $Function);

    /**
     * Specifies the grouping function
     *
     * Example expression function:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetBrand();
     * }
     * </code>
     *
     * @param  callable   $Function The grouping function
     * @return IQueryable
     */
    public function GroupBy(callable $Function);

    /**
     * Returns unique values
     *
     * @return IQueryable
     */
    public function Unique();

    /**
     * Maps the data with the supplied function.
     *
     * Or the entity for for the simple property retrieval:
     * <code>
     *  function (Car $Car) {
     *      return [
     *          'Brand' => $Car->GetBrand(),
     *          'Model Number' => $Car->GetModelNumber(),
     *          'Sale Price' => $Car->GetRetailPrice() - $Car->GetDiscountPrice(),
     *      ];
     *  }
     * </code>
     *
     * @param  callable   $Function The function returning the data to select
     * @return IQueryable
     */
    public function Select(callable $Function);

    /**
     * Maps the data with the supplied function and flattens the results.
     *
     * @param  callable   $Function The function returning the data to select
     * @return IQueryable
     */
    public function SelectMany(callable $Function);

    /**
     * Returns the first value
     *
     * @return mixed The first value
     */
    public function First();

    /**
     * Returns the amount of the values.
     *
     * @return int
     */
    public function Count();

    /**
     * Returns whether any values exist.
     *
     * @return boolean
     */
    public function Exists();

    /**
     * Returns whether the supplied value is contained within the aggregate
     *
     * @param  mixed   $Value The value to check for
     * @return boolean
     */
    public function Contains($Value);

    /**
     * Aggregates the values with the supplied function
     *
     * @param  callable $Function The aggregate function
     * @return mixed
     */
    public function Aggregate(callable $Function);

    /**
     * Returns the maximum value.
     *
     * @param  callable $Function The function which will return the values
     * @return int|null
     */
    public function Maximum(callable $Function = null);

    /**
     * Returns the maximum value.
     *
     * @param  callable $Function The function which will return the values
     * @return int|null
     */
    public function Minimum(callable $Function = null);

    /**
     * Returns the sum of the values.
     *
     * @param  callable $Function The function which will return the values
     * @return int|null
     */
    public function Sum(callable $Function = null);

    /**
     * Returns the average of the values.
     *
     * @param  callable    $Function The function which will return the values
     * @return double|null
     */
    public function Average(callable $Function = null);

    /**
     * Returns a boolean of if all the values evaluate to true
     *
     * @param  callable     $Function The function which will return the values
     * @return boolean|null
     */
    public function All(callable $Function = null);

    /**
     * Returns a boolean of if any the values evaluate to true
     *
     * @param  callable     $Function The function which will return the values
     * @return boolean|null
     */
    public function Any(callable $Function = null);

    /**
     * Returns a string of all the values concatented with the separator
     *
     * @param  string   $Delimiter The string to delimit the values by
     * @param  callable $Function  The function which will return the values
     * @return string
     */
    public function Implode($Delimiter, callable $Function = null);

    /**
     * Unions the results with the supplied collection
     *
     * @param  ITraversable $Traversable
     * @return IQueryable
     */
    public function Union(ITraversable $Traversable);

    /**
     * Append the results of the supplied collection
     *
     * @param  ITraversable $Traversable
     * @return IQueryable
     */
    public function Append(ITraversable $Traversable);

    /**
     * Intersects the results of the supplied collection
     *
     * @param  ITraversable $Traversable
     * @return IQueryable
     */
    public function Intersect(ITraversable $Traversable);

    /**
     * Removes the matching results from the supplied collection
     *
     * @param  ITraversable $Traversable
     * @return IQueryable
     */
    public function Except(ITraversable $Traversable);
}
