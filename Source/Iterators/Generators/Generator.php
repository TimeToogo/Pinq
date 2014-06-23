<?php

namespace Pinq\Iterators\Generators;

/**
 * Base class for an iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Generator implements IGenerator
{
    public function __construct()
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    abstract public function &getIterator();
}
