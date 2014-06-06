<?php

namespace Pinq\Iterators\Common\Joins;

/**
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IInnerValuesJoiner
{
    /**
     * Initializes the inner values iterator
     * 
     * @param \Traversable $innerValuesIterator
     * @return void
     */
    public function initialize(\Traversable $innerValuesIterator);
    
    /**
     * Gets the iterator for the inner values of the join
     * 
     * @param mixed $outerValue
     * @param mixed $outerKey
     * @return \Traversable
     */
    public function getInnerGroupIterator($outerValue, $outerKey);
}
