<?php

namespace Pinq;

/**
 * The standard collection class, fully implements the collection API
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Collection extends Traversable implements ICollection, Interfaces\IOrderedCollection
{
    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function asCollection()
    {
        return $this;
    }
    
    private function updateValues(\Iterator $values = null)
    {
        $this->valuesIterator = $values instanceof Iterators\Utilities\OrderedMap ? 
                $values : new Iterators\Utilities\OrderedMap($values);
    }

    public function clear()
    {
        $this->updateValues(new \EmptyIterator());
    }

    public function apply(callable $function)
    {
        //Fix for being unable to pass a variable number of args by ref
        if ($function instanceof FunctionExpressionTree) {
            $function = $function->getCompiledFunction();
        }
        
        $function = Iterators\Utilities\Functions::allowExcessiveArguments($function);
        
        $values = [];
        
        $orderedMap = $this->toOrderedMap();
        $this->iterate(function ($value, $key) use (&$values, $function) {
            $function($value, $key);
            $values[] = $value;
        });

        $this->valuesIterator = Iterators\Utilities\OrderedMap::from($orderedMap->keys(), $values);
    }

    public function addRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }

        $flattenedIterator = 
                new Iterators\FlatteningIterator(
                        new \ArrayIterator([
                                $this->valuesIterator, 
                                Utilities::toIterator($values)]));
        
        $this->updateValues(new \ArrayIterator(Utilities::toArray($flattenedIterator)));
    }

    public function removeRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }
        
        $this->updateValues(
                new Iterators\ExceptIterator(
                        $this->valuesIterator,
                        Utilities::toIterator($values)));
    }

    public function removeWhere(callable $predicate)
    {
        $keys = [];
        $values = [];
        
        $this->iterate(function ($value, $key) use (&$values, &$keys, $predicate) {
            if(!$predicate($value, $key)) {
                $values[] = $value;
                $keys[] = $key;
            }
        });

        $this->valuesIterator = Iterators\Utilities\OrderedMap::from($keys, $values);
    }

    public function offsetSet($index, $value)
    {
        $this->toOrderedMap()->offsetSet($index, $value);
    }

    public function offsetUnset($index)
    {
        $this->toOrderedMap()->offsetUnset($index);
    }
}
