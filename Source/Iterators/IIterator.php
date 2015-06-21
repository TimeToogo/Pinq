<?php

namespace Pinq\Iterators;

/**
 * Interface for an iterator.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IIterator
{
    /**
     * Returns whether the iterator can be foreach'd to
     * create an array (only string/int keys).
     *
     * @return bool
     */
    public function isArrayCompatible();
}
