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
    
    public function AsRepository()
    {
        return (new Providers\Collection\Provider($this))->CreateRepository();
    }
    
    public function Clear()
    {
        $this->ValuesIterator = new \EmptyIterator();
    }
    
    
    
    public function Apply(callable $Function)
    {
        $Array = $this->AsArray();
        
        //Fix for being unable to pass a variable number of args by ref
        if($Function instanceof FunctionExpressionTree) {
            $Function = $Function->GetCompiledFunction();
        }
        
        array_walk($Array, $Function);
        
        $this->ValuesIterator = new \ArrayIterator($Array);
    }
    
    /**
     * @param string $Argument
     */
    private function InvalidRange($Method, $Argument, $Value) 
    {
        return new PinqException(
                'Invalid argument to %s: %s must be an array or instance of traversable, %s given',
                $Method,
                $Argument,
                Utilities::GetTypeOrClass($Value));
    }

    public function AddRange($Values)
    {
        if(!Utilities::IsIterable($Values)) {
            throw $this->InvalidRange(__METHOD__, 'Values', $Values);
        }
        
        $FlattenedIterator = new Iterators\FlatteningIterator(new \ArrayIterator([$this->ValuesIterator, Utilities::ToIterator($Values)]));
        
        $this->ValuesIterator = new \ArrayIterator(Utilities::ToArray($FlattenedIterator));
    }

    public function RemoveRange($Values)
    {
        if(!Utilities::IsIterable($Values)) {
            throw $this->InvalidRange(__METHOD__, 'Values', $Values);
        }
        
        $ExceptIterator = new Iterators\ExceptIterator($this->ValuesIterator, Utilities::ToIterator($Values));
        
        $this->ValuesIterator = new \ArrayIterator(Utilities::ToArray($ExceptIterator));
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
