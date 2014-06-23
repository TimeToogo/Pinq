<?php

namespace Pinq\Iterators;


/**
 * Interface for an ordered iterator that supports subsequent ordering.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IWrapperIterator extends \Traversable
{
    /**
     * Returns the inner iterator.
     * 
     * @return \Traversable
     */
    public function getSourceIterator();
    
    /**
     * Returns a new instance of the iterator with the supplied 
     * source iterator.
     * 
     * @return static
     */
    public function updateSourceIterator(\Traversable $sourceIterator);
}
