<?php

namespace Pinq\Iterators;

/**
 * Interface for a factory for the required range of iterator classes
 * to fulfill the ITraversable interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IIteratorScheme
{
    const IITERATOR_SCHEME_TYPE = __CLASS__;

    /**
     * Creates an ordered map from the supplied iterator.
     * Keys can only be associated with a single value and such
     * if the iterator returns duplicate keys the last respective
     * value will be used.
     *
     * @param \Traversable|null $iterator
     *
     * @return IOrderedMap
     */
    public function createOrderedMap(\Traversable $iterator = null);

    /**
     * Creates an set from the supplied iterator values.
     * A set can only contain unique values and such duplicates
     * will be lost.
     *
     * @param \Traversable|null $iterator
     *
     * @return ISet
     */
    public function createSet(\Traversable $iterator = null);

    /**
     * Returns the supplied traversable or array as an iterator
     * compatible with this scheme.
     *
     * @param \Traversable|array $traversableOrArray
     *
     * @return \Traversable
     */
    public function toIterator($traversableOrArray);

    /**
     * Safely converts the supplied iterator to an array.
     * Non integer or string keys will be reindexed to
     * respective incremented integers.
     *
     * @param \Traversable|null $iterator
     *
     * @return array
     */
    public function toArray(\Traversable $iterator);

    /**
     * Iterate over the keys and values of the supplied iterator and
     * passes them (value, key) to the supplied function.
     *
     * @param \Traversable|null $iterator
     * @param callable          $function
     *
     * @return void
     */
    public function walk(\Traversable $iterator, callable $function);

    /**
     * Returns an iterator for the supplied array.
     *
     * @param array $array
     *
     * @return \Traversable
     */
    public function arrayIterator(array $array);

    /**
     * Returns an iterator which will map any non integer or string keys
     * to incrementing integers.
     *
     * @param \Traversable $iterator
     *
     * @return IWrapperIterator
     */
    public function arrayCompatibleIterator(\Traversable $iterator);

    /**
     * Returns an empty iterator.
     *
     * @return \Traversable
     */
    public function emptyIterator();

    /**
     * Returns an iterator which will filter the elements according to
     * the supplied predicate function.
     *
     * @param \Traversable $iterator
     * @param callable     $predicate
     *
     * @return IWrapperIterator
     */
    public function filterIterator(\Traversable $iterator, callable $predicate);

    /**
     * Returns an iterator with will sort the elements according to
     * the supplied function and direction.
     *
     * @param \Traversable $iterator
     * @param callable     $function
     * @param boolean      $isAscending
     *
     * @return IOrderedIterator
     */
    public function orderedIterator(\Traversable $iterator, callable $function, $isAscending);

    /**
     * Returns an iterator which will group the elements according to
     * the supplied function and wrap each group in a traversable
     * implementation from the supplied factory.
     *
     * @param \Traversable $iterator
     * @param callable     $groupKeyFunction
     * @param callable     $traversableFactory
     *
     * @return IWrapperIterator
     */
    public function groupedIterator(
            \Traversable $iterator,
            callable $groupKeyFunction,
            callable $traversableFactory
    );

    /**
     * Returns an iterator which will only iterate the elements in the
     * supplied range.
     *
     * @param \Traversable $iterator
     * @param int          $start
     * @param int|null     $amount
     *
     * @return IWrapperIterator
     */
    public function rangeIterator(\Traversable $iterator, $start, $amount);

    /**
     * Returns an iterator which will return the elements mapped by
     * the supplied functions or the original if no function is supplied.
     *
     * @param \Traversable  $iterator
     * @param callable|null $keyProjectionFunction
     * @param callable|null $valueProjectionFunction
     *
     * @return IWrapperIterator
     */
    public function projectionIterator(
            \Traversable $iterator,
            callable $keyProjectionFunction = null,
            callable $valueProjectionFunction = null
    );

    /**
     * Returns an iterator which will return the map the keys
     * to 0-based incrementing integers
     *
     * @param \Traversable $iterator
     *
     * @return IWrapperIterator
     */
    public function reindexerIterator(\Traversable $iterator);

    /**
     * Returns an iterator which will return only the first associated value
     * for any key.
     *
     * @param \Traversable $iterator
     *
     * @return IWrapperIterator
     */
    public function uniqueKeyIterator(\Traversable $iterator);

    /**
     * Returns an iterator which will return the outer elements joined
     * to the inner elements.
     *
     * @param \Traversable $outerIterator
     * @param \Traversable $innerIterator
     *
     * @return IJoinIterator
     */
    public function joinIterator(
            \Traversable $outerIterator,
            \Traversable $innerIterator
    );

    /**
     * Returns an iterator which will return the outer elements joined
     * to the inner elements. All matched inner elements for each
     * outer element will be wrapped in a traversable implementation from the supplied factory.
     *
     * @param \Traversable $outerIterator
     * @param \Traversable $innerIterator
     * @param callable     $traversableFactory
     *
     * @return IJoinIterator
     */
    public function groupJoinIterator(
            \Traversable $outerIterator,
            \Traversable $innerIterator,
            callable $traversableFactory
    );

    /**
     * Returns an iterator which will iterate each element
     * of the inner iterator's values. An exception will be thrown
     * if an invalid iterator is returned from the supplied iterator.
     *
     * @param \Traversable $iterator
     *
     * @return IWrapperIterator
     */
    public function flattenedIterator(\Traversable $iterator);

    /**
     * Returns an iterator which will return unique values using
     * strict equality.
     *
     * @param \Traversable $iterator
     *
     * @return IWrapperIterator
     */
    public function uniqueIterator(\Traversable $iterator);

    /**
     * Returns an iterator which will return all values from both
     * the supplied iterators.
     *
     * @param \Traversable $iterator
     * @param \Traversable $otherIterator
     *
     * @return IWrapperIterator
     */
    public function appendIterator(\Traversable $iterator, \Traversable $otherIterator);

    /**
     * Returns an iterator which will return all values in the first
     * iterator present in the second.
     *
     * @param \Traversable $iterator
     * @param \Traversable $otherIterator
     *
     * @return IWrapperIterator
     */
    public function whereInIterator(\Traversable $iterator, \Traversable $otherIterator);

    /**
     * Returns an iterator which will return all values in the first
     * but not present in the second iterator.
     *
     * @param \Traversable $iterator
     * @param \Traversable $otherIterator
     *
     * @return IWrapperIterator
     */
    public function exceptIterator(\Traversable $iterator, \Traversable $otherIterator);

    /**
     * Returns an iterator which will return unique values present
     * in the first or second iterator.
     *
     * @param \Traversable $iterator
     * @param \Traversable $otherIterator
     *
     * @return IWrapperIterator
     */
    public function unionIterator(\Traversable $iterator, \Traversable $otherIterator);

    /**
     * Returns an iterator which will return unique values present
     * in the first and second iterator.
     *
     * @param \Traversable $iterator
     * @param \Traversable $otherIterator
     *
     * @return IWrapperIterator
     */
    public function intersectionIterator(\Traversable $iterator, \Traversable $otherIterator);

    /**
     * Returns an iterator which will return uniqe values present
     * in the first but not the second iterator.
     *
     * @param \Traversable $iterator
     * @param \Traversable $otherIterator
     *
     * @return IWrapperIterator
     */
    public function differenceIterator(\Traversable $iterator, \Traversable $otherIterator);
}
