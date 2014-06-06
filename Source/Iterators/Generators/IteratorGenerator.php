<?php

namespace Pinq\Iterators\Generators;

/**
 * Base class for wrapper generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IteratorGenerator extends Generator
{
    /**
     * @var \Traversable
     */
    private $iterator;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct();
        $this->iterator = $iterator;
    }
    
    /**
     * @return \Traversable
     */
    final public function getInnerIterator()
    {
        return $this->iterator;
    }
    
    final public function getIterator()
    {
        return $this->iteratorGenerator($this->iterator);
    }
    
    abstract protected function iteratorGenerator(\Traversable $iterator);
}
