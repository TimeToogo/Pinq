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
    
    protected function fetchInner(\Iterator $iterator, &$key, &$value)
    {
        while (parent::fetchInner($iterator, $key, $value)) {
            if ($this->setFilter($key, $value, $this->otherValues)) {
                return true;
            }

            $iterator->next();
        }

        return false;
    }

    abstract protected function setFilter($key, $value, Utilities\Set $otherValues);

    final public function onRewind()
    {
        $this->otherValues = new Utilities\Set($this->otherIterator);
        parent::onRewind();
    }
}
