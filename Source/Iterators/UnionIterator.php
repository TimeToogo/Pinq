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
        parent::__construct(new ArrayIterator([$iterator, $otherIterator]));
    }

    public function doRewind()
    {
        $this->seenValues = new Utilities\Set();
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        while (parent::doFetch($key, $value)) {
            if ($this->seenValues->add($value)) {
                return true;
            }
        }

        return false;
    }
}
