<?php

namespace Pinq\Iterators;

class UniqueIterator extends OperationIterator
{
    /**
     * @var Utilities\Set 
     */
    private $SeenValues;
    
    public function __construct(\Traversable $Iterator)
    {
        parent::__construct($Iterator, new \ArrayIterator());
        $this->SeenValues = new Utilities\Set();
    }
    
    protected function SetFilter($Value, Utilities\Set $SeenValues)
    {
        return $SeenValues->Add($Value);
    }
}
