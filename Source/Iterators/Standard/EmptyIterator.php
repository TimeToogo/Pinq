<?php

namespace Pinq\Iterators\Standard;

/**
 * Implementation of the empty iterator using the fetch method.
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
