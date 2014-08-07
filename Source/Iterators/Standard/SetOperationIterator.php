<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\Common\SetOperations\ISetFilter;

/**
 * Implementation of the set operation iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SetOperationIterator extends IteratorIterator
{
    use Common\SetOperations\SetOperationIterator;

    public function __construct(IIterator $iterator, ISetFilter $setFilter)
    {
        parent::__construct($iterator);
        self::__constructIterator($setFilter);
    }

    final public function doRewind()
    {
        $this->setFilter->initialize();
        parent::doRewind();
    }

    protected function doFetch()
    {
        while ($element = $this->iterator->fetch()) {
            if ($this->setFilter->filter($element[0], $element[1])) {
                return $element;
            }
        }
    }

}
