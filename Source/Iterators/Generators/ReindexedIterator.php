<?php

namespace Pinq\Iterators\Generators;

/**
 * Implementation of the reindexer iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ReindexedIterator extends IteratorGenerator
{
    public function __construct(IGenerator $iterator)
    {
        parent::__construct($iterator);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        foreach ($iterator as &$value) {
            yield $value;
        }
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return true;
    }
}
