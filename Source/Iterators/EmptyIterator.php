<?php

namespace Pinq\Iterators;

/**
 * Implementation of an empty iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EmptyIterator extends Iterator
{
    protected function doFetch(&$key, &$value)
    {
        return false;
    }
}
