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
    
    /**
     * @return \Traversable
     */
    abstract protected function initializeIterator(\Traversable $innerIterator);
    
    public function doRewind()
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        
        return $this->iterator->fetch($key, $value);
    }
}
