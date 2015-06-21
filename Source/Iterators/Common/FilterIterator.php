<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIterator;

/**
 * Common functionality for the filter iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait FilterIterator
{
    /**
     * @var callable
     */
    protected $filter;

    final protected function __constructIterator(callable $filter)
    {
        $this->filter = Functions::allowExcessiveArguments($filter);
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
