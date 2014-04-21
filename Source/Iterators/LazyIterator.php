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
    protected $Iterator;
    
    /**
     * @var boolean
     */
    private $IsInitialized = false;
    
    public function __construct(\Traversable $Iterator)
    {
        $this->Iterator = $Iterator;
    }
        
    private function Initialize() {
        $Iterator = $this->InitializeIterator($this->Iterator) ?: $this->Iterator;
        parent::__construct($Iterator);
        $this->IsInitialized = true;
    }
    protected abstract function InitializeIterator(\Traversable $InnerIterator);
    
    public function GetInnerIterator()
    {
        if(!$this->IsInitialized) {
            $this->Initialize();
        }
        return parent::GetInnerIterator();
    }
    
    public function current()
    {
        if(!$this->IsInitialized) {
            $this->Initialize();
        }
        return parent::current();
    }

    public function key()
    {
        if(!$this->IsInitialized) {
            $this->Initialize();
        }
        return parent::key();
    }

    public function next()
    {
        if(!$this->IsInitialized) {
            $this->Initialize();
        }
        return parent::next();
    }

    public function rewind()
    {
        if(!$this->IsInitialized) {
            $this->Initialize();
        }
        return parent::rewind();
    }

}
