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
    
    /**
     * @param integer $startAmount
     * @param null|integer $rangeAmount
     */
    public function __construct(\Traversable $iterator, $startAmount, $rangeAmount)
    {
        parent::__construct($iterator);
        $this->startPosition = $startAmount;
        $this->endPosition = $rangeAmount === null ? null : $startAmount + $rangeAmount;
    }
    
    public function next()
    {
        $this->position++;
        
        return parent::next();
    }
    
    public function rewind()
    {
        $this->position = 0;
        
        return parent::rewind();
    }
    
    public function valid()
    {
        while ($this->position < $this->startPosition) {
            if (!parent::valid()) {
                return false;
            }
            
            $this->next();
        }
        
        if ($this->endPosition !== null && $this->position >= $this->endPosition) {
            return false;
        }
        
        return parent::valid();
    }
}