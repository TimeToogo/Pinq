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
    private $Position = 0;
    
    /**
     * @var int
     */
    private $StartPosition;
    
    /**
     * @var int|null
     */
    private $EndPosition;
    
    /**
     * @param integer $StartAmount
     * @param null|integer $RangeAmount
     */
    public function __construct(\Traversable $Iterator, $StartAmount, $RangeAmount)
    {
        parent::__construct($Iterator);
        $this->StartPosition = $StartAmount;
        $this->EndPosition = $RangeAmount === null ? null : $StartAmount + $RangeAmount;
    }

    public function next()
    {
        $this->Position++;
        return parent::next();
    }

    public function rewind()
    {
        $this->Position = 0;
        return parent::rewind();
    }

    public function valid()
    {
        while($this->Position < $this->StartPosition) {
            if(!parent::valid()) {
                return false;
            }
            $this->next();
        }
        
        if($this->EndPosition !== null && $this->Position >= $this->EndPosition) {
            return false;
        }
        
        return parent::valid();
    }
}
