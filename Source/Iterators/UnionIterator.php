<?php

namespace Pinq\Iterators;

class UnionIterator implements \Iterator
{
    private $OriginalValues = [];
    
    private $Valid = true;
    private $CurrentKey;
    private $CurrentValue;
    
    /**
     * @var \Iterator
     */
    private $OriginalIterator;
    
    private $IsOriginalIterator = false;
    /**
     * @var \Iterator
     */
    private $UnionedIterator;
    
    public function __construct(\Traversable $Iterator, \Traversable $UnionedIterator)
    {
        $this->OriginalIterator = $this->GetIterator($Iterator);
        $this->UnionedIterator = $this->GetIterator($UnionedIterator);
    }
    
    private function GetIterator(\Traversable $Traversable) 
    {
        if($Traversable instanceof \IteratorAggregate) {
            return $Traversable->getIterator();
        }
        else {
            return $Traversable;
        }
    }
    
    public function rewind()
    {
        $this->OriginalValues = [];
        
        $this->OriginalIterator->rewind();
        $this->UnionedIterator->rewind();
        
        $IsOriginalValid = $this->OriginalIterator->valid();
        if($IsOriginalValid) {
            $this->IsOriginalIterator = true;
            $this->Valid = true;
            $this->UpdateCurrentValues($this->OriginalIterator);
            return;
        }
        
        $this->IsOriginalIterator = false;
        $this->Valid = $this->UnionedIterator->valid();
        if($this->Valid) {
            $this->Valid = true;
            $this->UpdateCurrentValues($this->UnionedIterator);
            return;
        }
    }
    
    private function UpdateCurrentValues(\Iterator $Iterator) 
    {
        $this->CurrentKey = $Iterator->key();
        $this->CurrentValue = $Iterator->current();
        if($this->IsOriginalIterator) {
            $this->OriginalValues[$this->CurrentKey] = $this->CurrentValue;
        }
    }


    public function key()
    {
        return $this->CurrentKey;
    }
    
    public function current()
    {
        return $this->CurrentValue;
    }
    
    public function valid()
    {
        return $this->Valid;
    }
    
    public function next()
    {
        if($this->IsOriginalIterator) {
            $this->OriginalIterator->next();
            
            if($this->OriginalIterator->valid()) {
                $this->UpdateCurrentValues($this->OriginalIterator);
                $this->Valid = true;
                return;
            }
        }
        
        if($this->IsOriginalIterator) {
            $this->IsOriginalIterator = false;
        }
        else {
            $this->UnionedIterator->next();
        }
        
        while($this->UnionedIterator->valid()) {
            $this->UpdateCurrentValues($this->UnionedIterator);
            
            //Skip matching keys or values
            if(isset($this->OriginalIterator[$this->CurrentKey]) || in_array($this->CurrentValue, $this->OriginalValues, true)) {
                $this->UnionedIterator->next();
                continue;
            }
            
            $this->Valid = true;
            return;
        }
        
        $this->Valid = false;
    }
}
