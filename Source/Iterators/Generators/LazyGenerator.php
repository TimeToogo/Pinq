<?php

namespace Pinq\Iterators\Generators;

/**
 * Base class for a lazy generator, initialized upon first access.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class LazyGenerator extends IteratorGenerator
{
    /**
     * @var \Traversable
     */
    protected $loadedIterator;

    /**
     * @var boolean
     */
    private $isInitialized = false;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
    }
    
    final protected function iteratorGenerator(\Traversable $iterator)
    {
        if(!$this->isInitialized) {
            $this->loadedIterator = $this->initializeGenerator($iterator);
            $this->isInitialized = true;
        }
        
        foreach($this->loadedIterator as $key => $value) {
            yield $key => $value;
        }
    }
    
    /**
     * @return \Traversable
     */
    abstract protected function initializeGenerator(\Traversable $innerIterator);
}
