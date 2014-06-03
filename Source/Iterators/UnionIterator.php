<?php

namespace Pinq\Iterators;

/**
 * Iterates the unique values contained in either the first values or in the second values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnionIterator extends FlatteningIterator
{
    /**
     * @var Utilities\Set
     */
    private $seenValues;

    public function __construct(\Traversable $iterator, \Traversable $otherIterator)
    {
        parent::__construct(new \ArrayIterator([$iterator, $otherIterator]));
    }

    public function onRewind()
    {
        $this->seenValues = new Utilities\Set();
        parent::onRewind();
    }
    
    protected function fetch(&$key, &$value)
    {
        while (parent::fetch($key, $value)) {
            if ($this->seenValues->add($value)) {
                return true;
            }
            
            $this->currentIterator->next();
        }

        return false;
    }
}
