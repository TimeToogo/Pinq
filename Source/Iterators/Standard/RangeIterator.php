<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the range iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RangeIterator extends IteratorIterator
{
    use Common\RangeIterator;
    
    /**
     * @var int
     */
    private $position = 0;
    
    public function __construct(\Traversable $iterator, $startAmount, $rangeAmount)
    {
        parent::__construct($iterator);
        self::__constructIterator($startAmount, $rangeAmount);
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
