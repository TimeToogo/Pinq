<?php

namespace Pinq\Iterators;

/**
 * Iterates over a specified range of the inner values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RangeIterator extends IteratorIterator
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var int
     */
    private $startPosition;

    /**
     * @var int|null
     */
    private $endPosition;
    
    public function __construct(\Traversable $iterator, $startAmount, $rangeAmount)
    {
        parent::__construct($iterator);
        $this->startPosition = $startAmount;
        $this->endPosition = $rangeAmount === null ? null : $startAmount + $rangeAmount;
    }
    
    public function onRewind()
    {
        $this->position = 0;
        parent::onRewind();
    }
    
    public function onNext()
    {
        parent::onNext();
        $this->position++;
    }
    
    protected function fetchInner(\Iterator $iterator, &$key, &$value)
    {
        while ($this->position < $this->startPosition) {
            if (!$iterator->valid()) {
                return false;
            }

            $iterator->next();
            $this->position++;
        }

        if ($this->endPosition !== null && $this->position >= $this->endPosition) {
            return false;
        }

        return parent::fetchInner($iterator, $key, $value);
    }
}
