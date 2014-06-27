<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the coalesce iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait CoalesceIterator
{
    /**
     * @var mixed
     */
    protected $defaultValue;
    
    /**
     * @var mixed
     */
    protected $defaultKey;

    public function __constructIterator($defaultValue, $defaultKey)
    {
        $this->defaultValue = $defaultValue;
        $this->defaultKey = $defaultKey;
    }
}
