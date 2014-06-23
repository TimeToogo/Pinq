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
    public function __construct(IGenerator $iterator)
    {
        parent::__construct($iterator);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        foreach($iterator as &$value) {
            yield $value;
        }
    }
}
