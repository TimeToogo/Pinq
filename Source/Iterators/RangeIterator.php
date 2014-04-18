<?php

namespace Pinq\Iterators;

class RangeIterator extends IteratorIterator
{
    /**
     * @var int
     */
    private $Position = 0;
    
    /**
     * @var int
     */
    private $StartAmount;
    
    /**
     * @var int|null
     */
    private $RangeAmount;
    
    /**
     * @param integer $StartAmount
     * @param null|integer $RangeAmount
     */
    public function __construct(\Traversable $Iterator, $StartAmount, $RangeAmount)
    {
        parent::__construct($Iterator);
        $this->StartAmount = $StartAmount;
        $this->RangeAmount = $RangeAmount;
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
        while($this->Position < $this->StartAmount) {
            if(!parent::valid()) {
                false;
            }
            $this->next();
        }
        
        if($this->RangeAmount !== null && $this->Position >= $this->RangeAmount) {
            return false;
        }
        
        return parent::valid();
    }
}
