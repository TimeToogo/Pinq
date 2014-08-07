<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

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
        yield;
    }
}
