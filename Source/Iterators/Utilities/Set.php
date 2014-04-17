<?php

namespace Pinq\Iterators\Utilities;

class Set implements \IteratorAggregate
{
    /**
     * @var Dictionary
     */
    private $Dictionary;
    
    public function __construct($Values = null)
    {
        $this->Dictionary = new Dictionary();
        if($Values !== null) {
            $this->AddRange($Values);
        }
    }
    
    public function Contains($Value) 
    {
        return $this->Dictionary->Contains($Value);
    }

    public function Add($Value) 
    {
        if($this->Dictionary->Contains($Value)) {
            return false;
        }
        $this->Dictionary->Set($Value, true);
        
        return true;
    }
    
    public function AddRange($Values) 
    {
        foreach ($Values as $Value) {
            $this->Dictionary->Set($Value, true);
        }
    }
    
    public function Remove($Value) 
    {
        if(!$this->Dictionary->Contains($Value)) {
            return false;
        }
        $this->Dictionary->Remove($Value);
        
        return true;
    }
    
    public function RemoveRange($Values) 
    {
        foreach ($Values as $Value) {
            $this->Dictionary->Remove($Value);
        }
    }
    
    public function getIterator()
    {
        return $this->Dictionary->getIterator();
    }
}
