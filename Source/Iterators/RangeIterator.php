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
    
    public function doRewind()
    {
        $this->position = 0;
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        while ($this->iterator->fetch($key, $value)) {
            if($this->endPosition !== null && $this->position >= $this->endPosition) {
                return false;
            } elseif ($this->position >= $this->startPosition) {
                $this->position++;
                return true;
            } else {
                $this->position++;
            }
            
        }
        
        return false;
    }
}
