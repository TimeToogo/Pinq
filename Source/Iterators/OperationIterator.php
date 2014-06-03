<?php

namespace Pinq\Iterators;

/**
 * Base class for a set operation iterator, the other values
 * are stored in a set which can be used to filter the resulting values
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class OperationIterator extends IteratorIterator
{
    /**
     * @var \Traversable
     */
    private $otherIterator;

    /**
     * @var Utilities\Set
     */
    private $otherValues;
    
    public function __construct(\Traversable $iterator, \Traversable $otherIterator)
    {
        parent::__construct($iterator);
        $this->otherIterator = $otherIterator;
    }

    final public function doRewind()
    {
        $this->otherValues = new Utilities\Set($this->otherIterator);
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        while($this->iterator->fetch($key, $value)) {
            if($this->setFilter($key, $value, $this->otherValues)) {
                return true;
            }
        }
        
        return false;
    }

    abstract protected function setFilter($key, $value, Utilities\Set $otherValues);
}
