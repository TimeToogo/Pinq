<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ISetFilter
{
    /**
     * Initializes the set filter.
     * 
     * @return void
     */
    public function initialize();
    
    /**
     * Returns whether the key and value is valid.
     * 
     * @param mixed $key
     * @param mixed $value
     * @return boolean
     */
    public function filter($key, $value);
}
