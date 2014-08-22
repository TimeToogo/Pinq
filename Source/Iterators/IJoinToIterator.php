<?php

namespace Pinq\Iterators;

/**
 * Interface for an ordered iterator that supports subsequent ordering.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoinToIterator extends IWrapperIterator
{
    /**
     * Returns a new join iterator that will join the supplied element if no
     * matching inner elements are given.
     *
     * @param mixed $value
     * @param mixed $key
     *
     * @return IJoinToIterator
     */
    public function withDefault($value, $key = null);

    /**
     * Returns a new join iterator that will project the values with the supplied
     * function, called with the parameters ($outerValue, $innerValue, $outerKey, $innerKey).
     *
     * @param callable $function
     *
     * @return \Traversable
     */
    public function projectTo(callable $function);

    /**
     * Walks the joined elements
     *
     * @param callable $function
     *
     * @return void
     */
    public function walk(callable $function);
}
