<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Base class for a lazy iterator, initialized upon rewind.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class LazyIterator extends IteratorIterator
{
    /**
     * @var IIterator
     */
    private $originalIterator;
    
    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
        $this->originalIterator = $iterator;
    }
    
    /**
     * @return IIterator
     */
    abstract protected function initializeIterator(IIterator $innerIterator);
    
    public function doRewind()
    {
        $this->iterator = $this->initializeIterator($this->originalIterator);
        
        parent::doRewind();
    }
    
    protected function doFetch()
    {
        return $this->iterator->fetch();
    }
}
