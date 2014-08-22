<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the range iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RangeIterator extends IteratorIterator
{
    use Common\RangeIterator;

    /**
     * @var int
     */
    private $position = 0;

    public function __construct(IIterator $iterator, $startAmount, $rangeAmount)
    {
        parent::__construct($iterator);
        self::__constructIterator($startAmount, $rangeAmount);
    }

    public function doRewind()
    {
        $this->position = 0;
        parent::doRewind();
    }

    protected function doFetch()
    {
        while ($element = $this->iterator->fetch()) {
            if ($this->endPosition !== null && $this->position >= $this->endPosition) {
                return null;
            } elseif ($this->position >= $this->startPosition) {
                $this->position++;

                return $element;
            } else {
                $this->position++;
            }
        }
    }
}
