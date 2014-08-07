<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the filter iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FilterIterator extends IteratorGenerator
{
    use Common\FilterIterator;

    public function __construct(IGenerator $iterator, callable $filter)
    {
        parent::__construct($iterator);
        self::__constructIterator($filter);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        $filter = $this->filter;

        foreach ($iterator as $key => &$value) {
            $keyCopy   = $key;
            $valueCopy = $value;

            if ($filter($valueCopy, $keyCopy)) {
                yield $key => $value;
            }
        }
    }
}
