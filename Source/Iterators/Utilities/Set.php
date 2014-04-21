<?php

namespace Pinq\Iterators\Utilities;

/**
 * Represents set of unique values.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Set implements \IteratorAggregate
{
    /**
     * The dictionary containing the unique values as keys
     * 
     * @var Dictionary<mixed, true>
     */
    private $Dictionary;
    
    public function __construct($Values = null)
    {
        $this->Dictionary = new Dictionary();
        if($Values !== null) {
            $this->AddRange($Values);
        }
    }
    
    /**
     * Returns whether the values in contained in the set
     * 
     * @param mixed $Value
     * @return boolean 
     */
    public function Contains($Value) 
    {
        return $this->Dictionary->Contains($Value);
    }

    /**
     * Attempts to add the value to the set, will fail if the value
     * is already contained in the set
     * 
     * @param mixed $Value
     * @return boolean Whether the value was successfully added
     */
    public function Add($Value) 
    {
        if($this->Dictionary->Contains($Value)) {
            return false;
        }
        $this->Dictionary->Set($Value, true);
        
        return true;
    }
    
    /**
     * Attempts to add a range of the value to the set
     * 
     * @param array|\Traversable $Values
     * @return void
     */
    public function AddRange($Values) 
    {
        foreach ($Values as $Value) {
            $this->Dictionary->Set($Value, true);
        }
    }
    

    /**
     * Attempts to remove the value from the set, will fail if the value
     * is not contained in the set
     * 
     * @param mixed $Value
     * @return boolean Whether the value was successfully removed
     */
    public function Remove($Value) 
    {
        if(!$this->Dictionary->Contains($Value)) {
            return false;
        }
        $this->Dictionary->Remove($Value);
        
        return true;
    }
    
    /**
     * Attempts to remove a range of the value to the set
     * 
     * @param array|\Traversable $Values
     * @return void
     */
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
