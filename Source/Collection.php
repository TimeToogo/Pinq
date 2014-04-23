<?php 

namespace Pinq;

/**
 * The standard collection class, fully implements the collection API
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Collection extends Traversable implements ICollection
{
    public function __construct($values = [])
    {
        parent::__construct($values);
    }
    
    public function asCollection()
    {
        return $this;
    }
    
    public function asRepository()
    {
        return (new Providers\Collection\Provider($this))->createRepository();
    }
    
    public function clear()
    {
        $this->valuesIterator = new \EmptyIterator();
    }
    
    public function apply(callable $function)
    {
        $array = $this->asArray();
        
        //Fix for being unable to pass a variable number of args by ref
        if ($function instanceof FunctionExpressionTree) {
            $function = $function->getCompiledFunction();
        }
        
        array_walk($array, $function);
        $this->valuesIterator = new \ArrayIterator($array);
    }
    
    public function addRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }
        
        $flattenedIterator = new Iterators\FlatteningIterator(new \ArrayIterator([$this->valuesIterator, Utilities::toIterator($values)]));
        $this->valuesIterator = new \ArrayIterator(Utilities::toArray($flattenedIterator));
    }
    
    public function removeRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }
        
        $exceptIterator = 
                new Iterators\ExceptIterator(
                        $this->valuesIterator,
                        Utilities::toIterator($values));
        $this->valuesIterator = new \ArrayIterator(Utilities::toArray($exceptIterator));
    }
    
    public function removeWhere(callable $predicate)
    {
        $array = $this->asArray();
        
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                unset($array[$key]);
            }
        }
        
        $this->valuesIterator = new \ArrayIterator($array);
    }
    
    public function offsetSet($index, $value)
    {
        if ($this->valuesIterator instanceof \ArrayAccess) {
            $this->valuesIterator->offsetSet($index, $value);
        }
        else {
            $array = $this->asArray();
            $array[$index] = $value;
            $this->valuesIterator = new \ArrayIterator($array);
        }
    }
    
    public function offsetUnset($index)
    {
        if ($this->valuesIterator instanceof \ArrayAccess) {
            $this->valuesIterator->offsetUnset($index);
        }
        else {
            $array = $this->asArray();
            unset($array[$index]);
            $this->valuesIterator = new \ArrayIterator($array);
        }
    }
}