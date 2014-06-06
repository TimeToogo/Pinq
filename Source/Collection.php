<?php

namespace Pinq;

use Pinq\Iterators\IIteratorScheme;

/**
 * The standard collection class, fully implements the collection API
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Collection extends Traversable implements ICollection, Interfaces\IOrderedCollection
{
    public function __construct($values = array(), Iterators\IIteratorScheme $scheme = null)
    {
        parent::__construct($values, $scheme);
    }

    public function asCollection()
    {
        return $this;
    }
    
    private function updateValues(\Traversable $values)
    {
        $this->valuesIterator = $this->scheme->createOrderedMap($values);
    }

    public function clear()
    {
        $this->valuesIterator = $this->scheme->createOrderedMap();
    }

    public function apply(callable $function)
    {
        //Fix for being unable to pass a variable number of args by ref
        if ($function instanceof FunctionExpressionTree) {
            $function = $function->getCompiledFunction();
        }
        
        $function = Iterators\Common\Functions::allowExcessiveArguments($function);
        
        $values = [];
        
        $orderedMap = $this->toOrderedMap();
        $this->iterate(function ($value, $key) use (&$values, $function) {
            $function($value, $key);
            $values[] = $value;
        });

        $this->valuesIterator = $this->scheme->createOrderedMapFrom($orderedMap->keys(), $values);
    }

    public function addRange($values)
    {
        $this->updateValues($this->scheme->appendIterator(
                $this->valuesIterator, 
                $this->scheme->toIterator($values)));
    }

    public function removeRange($values)
    {
        $this->updateValues($this->scheme->exceptIterator(
                $this->valuesIterator,
                $this->scheme->toIterator($values)));
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

        $this->valuesIterator = $this->scheme->createOrderedMapFrom($keys, $values);
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
