<?php

namespace Pinq\Iterators\Generators;

/**
 * Implementation of the empty iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EmptyIterator extends Generator
{
    public function &getIterator(): \Traversable
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
