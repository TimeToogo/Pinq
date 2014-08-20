<?php

namespace Pinq\Iterators;

/**
 * Interface for an ordered iterator that supports subsequent ordering.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOrderedIterator extends IWrapperIterator
{
    /**
     * Returns an iterator which will further sort the values according
     * to the supplied function and direction.
     *
     * @param callable $function
     * @param boolean  $isAscending
     *
     * @return IOrderedIterator
     */
    public function thenOrderBy(callable $function, $isAscending);
}
