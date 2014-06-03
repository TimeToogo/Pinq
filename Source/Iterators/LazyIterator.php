<?php

namespace Pinq\Iterators;

/**
 * Basy class for a lazy iterator, the initializer will be
 * called once, when first accessed.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class LazyIterator extends IteratorIterator
{
    /**
     * @var \Traversable
     */
    protected $iterator;

    /**
     * @var boolean
     */
    private $isInitialized = false;

    public function __construct(\Traversable $iterator)
    {
        $this->iterator = $iterator;
    }

    private function initialize()
    {
        $iterator = $this->initializeIterator($this->iterator) ?: $this->iterator;
        parent::__construct($iterator);
        $this->isInitialized = true;
    }

    abstract protected function initializeIterator(\Traversable $innerIterator);
    
    public function onRewind()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        parent::onRewind();
    }
}
