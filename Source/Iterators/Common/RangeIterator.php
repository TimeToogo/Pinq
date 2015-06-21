<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIterator;

/**
 * Iterates over a specified range of the inner values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait RangeIterator
{
    /**
     * @var int
     */
    protected $startPosition;

    /**
     * @var int|null
     */
    protected $endPosition;

    protected function __constructIterator($startAmount, $rangeAmount)
    {
        $this->startPosition = $startAmount;
        $this->endPosition   = $rangeAmount === null ? null : $startAmount + $rangeAmount;
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
