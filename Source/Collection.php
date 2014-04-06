<?php

namespace Pinq;

/**
 * An in-memory implementation for collection api.
 */
class Collection extends Traversable implements ICollection
{
    public function __construct($Values = [])
    {
        parent::__construct($Values);
    }
    
    public function AsCollection()
    {
        return $this;
    }
    
    public function Clear()
    {
        $this->ValuesIterator = new \EmptyIterator();
    }
    
    public function Apply(callable $Function)
    {
        $Array = $this->AsArray();
        array_walk($Array, $Function);
        
        $this->ValuesIterator = new \ArrayIterator($Array);
    }

    public function AddRange($Values)
    {
        $this->ValuesIterator = new \ArrayIterator(
                array_merge(
                        $this->AsArray(), 
                        is_array($Values) ? $Values : Utilities::ToArray($Values)));
    }

    public function RemoveRange($Values)
    {
        $FilteredArray = array_udiff(
                $this->AsArray(), 
                is_array($Values) ? $Values : Utilities::ToArray($Values), 
                Utilities::$Identical);
        
        $this->ValuesIterator = new \ArrayIterator($FilteredArray);
    }

    public function RemoveWhere(callable $Predicate)
    {
        $Array = $this->AsArray();
        foreach ($Array as $Key => $Value) {
            if($Predicate($Value, $Key)) {
                unset($Array[$Key]);
            }
        }
        
        $this->ValuesIterator = new \ArrayIterator($Array);
    }

    public function offsetSet($Index, $Value)
    {
        if($this->ValuesIterator instanceof \ArrayAccess) {
            $this->ValuesIterator->offsetSet($Index, $Value);
        }
        else {
            $Array = $this->AsArray();
            $Array[$Index] = $Value;

            $this->ValuesIterator = new \ArrayIterator($Array);
        }
    }

    public function offsetUnset($Index)
    {
        if($this->ValuesIterator instanceof \ArrayAccess) {
            $this->ValuesIterator->offsetUnset($Index);
        }
        else {
            $Array = $this->AsArray();
            unset($Array[$Index]);

            $this->ValuesIterator = new \ArrayIterator($Array);
        }
    }

}
