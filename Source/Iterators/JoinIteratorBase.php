<?php

namespace Pinq\Iterators;

abstract class JoinIteratorBase implements \Iterator
{
    private $Count = 0;
    
    /**
     * @var boolean
     */
    protected $IsInitialized = false;
    
    /**
     * @var \Iterator
     */
    protected $OuterIterator;
    
    /**
     * @var \Iterator
     */
    protected $InnerIterator;
    /**
     * @var mixed
     */
    private $CurrentOuterValue = null;
    
    /**
     * @var \Iterator
     */
    private $CurrentInnerGroupIterator;
    
    /**
     * @var callable
     */
    protected $JoiningFunction;    
    
    public function __construct(
            \Traversable $OuterIterator,
            \Traversable $InnerIterator,
            callable $JoiningFunction)
    {
        $this->OuterIterator = \Pinq\Utilities::ToIterator($OuterIterator);
        $this->InnerIterator = \Pinq\Utilities::ToIterator($InnerIterator);
        $this->JoiningFunction = $JoiningFunction;
    }
    
    final public function key()
    {
        return $this->Count;
    }
    
    final public function current()
    {
        $JoiningFunction = $this->JoiningFunction;
        
        return $JoiningFunction($this->CurrentOuterValue, $this->CurrentInnerGroupIterator->current());
    }

    final public function next()
    {
        $this->CurrentInnerGroupIterator->next();
        $this->Count++;
    }
    
    final public function valid()
    {
        while(!$this->CurrentInnerGroupIterator->valid()) {
            if(!$this->OuterIterator->valid()) {
                return false;
            }
            
            $this->CurrentOuterValue = $this->OuterIterator->current();
            $this->CurrentInnerGroupIterator = $this->GetInnerGroupIterator($this->CurrentOuterValue);
            $this->OuterIterator->next();
        }
        
        return true;
    }
    
    /**
     * @return \Iterator
     */
    protected abstract function GetInnerGroupIterator($OuterValue);
    
    
    public function rewind()
    {
        if(!$this->IsInitialized) {
            $this->Initialize();
            $this->IsInitialized = true;
        }
        $this->CurrentOuterValue = null;
        $this->CurrentInnerGroupIterator = new \ArrayIterator();
        $this->Count = 0;
    }
    
    protected abstract function Initialize();
}
