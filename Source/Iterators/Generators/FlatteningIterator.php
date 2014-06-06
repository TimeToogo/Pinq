<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the flattened iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FlatteningIterator extends IteratorGenerator
{
    protected function iteratorGenerator(\Traversable $iterator)
    {
        foreach($iterator as $innerIterator) {
            foreach($innerIterator as $value) {
                yield $value;
            }
        }
    }
}
