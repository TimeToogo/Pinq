<?php

namespace Pinq\Iterators;


/**
 * Interface for an adapter iterator.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IAdapterIterator
{
    /**
     * Gets the source iterator.
     * 
     * @return \Traversable
     */
    public function getSourceIterator();
}
