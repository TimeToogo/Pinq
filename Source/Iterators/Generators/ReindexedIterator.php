<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the reindexer iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ReindexedIterator extends IteratorGenerator
{
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
    }

    protected function iteratorGenerator(\Traversable $iterator)
    {
        foreach($iterator as $value) {
            yield $value;
        }
    }
}
