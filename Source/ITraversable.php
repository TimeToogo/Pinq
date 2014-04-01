<?php

namespace Pinq;

/**
 * Concrete classes should be immutable and return a new instance with every chained query call.
 */
interface ITraversable extends IConvertable, IAggregatable, \IteratorAggregate
{
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
     * @param  callable    $Predicate The predicate function
     * @return ITraversable
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
     * @param  callable           $Function The expression function
     * @return IOrderedTraversable
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
     * @param  callable           $Function The expression function
     * @return IOrderedTraversable
     */
    public function OrderByDescending(callable $Function);

    /**
     * Specifies the amount to skip.
     *
     * @param  int         $Amount The amount of values to skip
     * @return ITraversable
     */
    public function Skip($Amount);

    /**
     * Specifies the amount to retrieve. Pass null to remove the limit.
     *
     * @param  int|null    $Amount The amount of values to retrieve
     * @return ITraversable
     */
    public function Take($Amount);

    /**
     * Returns a slice of the enumerable
     *
     * @param  int         $Start  The amount of values to skip
     * @param  int|null    $Amount The amount of values to retrieve
     * @return ITraversable
     */
    public function Slice($Start, $Amount);

    /**
     * Will use the values of the supplied function as the index
     *
     * @param  callable    $Function The function returning the key data
     * @return ITraversable
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
     * @param  callable    $Function The grouping function
     * @return ITraversable
     */
    public function GroupBy(callable $Function);

    /**
     * Returns unique values
     *
     * @return ITraversable
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
     * @param  callable    $Function The function returning the data to select
     * @return ITraversable
     */
    public function Select(callable $Function);

    /**
     * Maps the data with the supplied function and flattens the results.
     *
     * @param  callable    $Function The function returning the data to select
     * @return ITraversable
     */
    public function SelectMany(callable $Function);

    /**
     * Unions the results with the supplied enumerable
     *
     * @param  ITraversable $Traversable
     * @return ITraversable
     */
    public function Union(ITraversable $Traversable);

    /**
     * Append the results of the supplied enumerable
     *
     * @param  ITraversable $Traversable
     * @return ITraversable
     */
    public function Append(ITraversable $Traversable);

    /**
     * Intersects the results of the supplied enumerable
     *
     * @param  ITraversable $Traversable
     * @return ITraversable
     */
    public function Intersect(ITraversable $Traversable);

    /**
     * Removes the matching results from the supplied enumerable
     *
     * @param  ITraversable $Traversable
     * @return ITraversable
     */
    public function Except(ITraversable $Traversable);

}
