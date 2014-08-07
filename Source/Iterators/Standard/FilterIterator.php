<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the filter iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FilterIterator extends IteratorIterator
{
    use Common\FilterIterator;

    public function __construct(IIterator $iterator, callable $filter)
    {
        parent::__construct($iterator);
        self::__constructIterator($filter);
    }

    protected function doFetch()
    {
        $filter = $this->filter;

        while ($element = $this->iterator->fetch()) {
            $keyCopy   = $element[0];
            $valueCopy = $element[1];

            if ($filter($valueCopy, $keyCopy)) {
                return $element;
            }
        }
    }
}
