<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\IWrapperIterator;

/**
 * Base class for wrapper generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IteratorGenerator extends Generator implements IWrapperIterator
{
    /**
     * @var \Traversable
     */
    protected $iterator;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct();
        $this->iterator = $iterator;
    }
    
    final public function getSourceIterator()
    {
        return $this->iterator;
    }
    
    final public function updateSourceIterator(\Traversable $sourceIterator)
    {
        $clone = clone $this;
        $clone->iterator = $sourceIterator;
        
        return $clone;
    }
    
    final public function &getIterator()
    {
        return $this->iteratorGenerator($this->iterator);
    }
    
    abstract protected function &iteratorGenerator(\Traversable $iterator);
}
