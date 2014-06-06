<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\ISet;
use Pinq\Iterators\IOrderedMap;

/**
 * Contains the common functionality for a ISet implementation
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait Set
{
    /**
     * The map containing the unique values as keys
     *
     * @var IOrderedMap
     */
    protected $map;
    
    
    public function count()
    {
        return $this->map->count();
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->map->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function contains($value)
    {
        return $this->map->contains($value);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        return $this->map->setIfNotContained($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($value)
    {
        return $this->map->remove($value);
    }
}
