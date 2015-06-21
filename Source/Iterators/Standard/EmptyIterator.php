<?php

namespace Pinq\Iterators\Standard;

/**
 * Implementation of the empty iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EmptyIterator extends Iterator
{
    protected function doFetch()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isArrayCompatible()
    {
        return true;
    }
}
