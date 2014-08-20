<?php

namespace Pinq\Iterators;

/**
 * Interface for an ordered iterator that supports subsequent ordering.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoinIterator extends IJoinToIterator
{
    /**
     * Returns a new join iterator with the inner elements filtered according
     * to the supplied predicate function.
     *
     * @param callable $function Called with the parameters ($outerValue, $innerValue, $outerKey, $innerKey)
     *
     * @return IJoinToIterator
     */
    public function filterOn(callable $function);

    /**
     * Returns a new join iterator with the inner elements filtered according
     * to strict equality between the outer and inner key function.
     *
     * @param callable $outerKeyFunction Called with the parameters ($outerValue, $outerKey)
     * @param callable $innerKeyFunction Called with the parameters ($innerValue, $innerKey)
     *
     * @return IJoinToIterator
     */
    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction);
}
