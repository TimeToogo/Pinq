<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIterator;

/**
 * Common functionality for a set operation iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait SetOperationIterator
{
    /**
     * @var ISetFilter
     */
    protected $setFilter;

    final protected function __constructIterator(ISetFilter $setFilter)
    {
        $this->setFilter = $setFilter;
    }

    /**
     * @return IIterator
     */
    abstract protected function getSourceIterator();

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return $this->getSourceIterator()->isArrayCompatible();
    }
}
