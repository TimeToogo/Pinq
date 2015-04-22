<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\Common\SetOperations\ISetFilter;

/**
 * Implementation of the set operation iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SetOperationIterator extends IteratorGenerator
{
    use Common\SetOperations\SetOperationIterator;

    public function __construct(IGenerator $iterator, ISetFilter $setFilter)
    {
        parent::__construct($iterator);
        self::__constructIterator($setFilter);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        $setFilter = clone $this->setFilter;
        $setFilter->initialize();

        foreach ($iterator as $key => &$value) {
            if ($setFilter->filter($key, $value)) {
                yield $key => $value;
            }
        }
    }
}
