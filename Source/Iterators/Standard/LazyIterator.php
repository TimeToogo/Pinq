<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Base class for a lazy iterator, initialized upon first access.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class LazyIterator extends IteratorIterator
{
    /**
     * @var boolean
     */
    private $isInitialized = false;
    
    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
    }

    private function initialize()
    {
        $this->iterator = $this->initializeIterator($this->iterator);
        $this->isInitialized = true;
    }
    
    /**
     * @return IIterator
     */
    abstract protected function initializeIterator(IIterator $innerIterator);
    
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
