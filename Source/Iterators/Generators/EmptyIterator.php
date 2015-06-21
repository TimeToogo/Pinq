<?php

namespace Pinq\Iterators\Generators;

/**
 * Implementation of the empty iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EmptyIterator extends Generator
{
    public function &getIterator()
    {
        return;
        yield null;
    }

    /**
     * @return bool
     */
    public function isArrayCompatible()
    {
        return true;
    }
}
