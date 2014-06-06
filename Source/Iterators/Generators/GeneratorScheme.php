<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Iterator scheme using rewindable generator implementations.
 * Compatible with >= PHP 5.5.0.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GeneratorScheme extends Common\IteratorScheme
{
    public static function compatiableWith($phpVersion)
    {
        return version_compare($phpVersion, '5.5.0', '>=');
    }
    
    public function createOrderedMap(\Traversable $iterator = null)
    {
        return new OrderedMap($iterator);
    }
    
    public function createOrderedMapFrom(array $keys, array $values)
    {
        return OrderedMap::from($keys, $values);
    }
    
    public function createSet(\Traversable $iterator = null)
    {
        return new Set($iterator);
    }

    public function walk(\Traversable $iterator, callable $function)
    {
        foreach($iterator as $key => $value) {
            if($function($value, $key) === false) {
                break;
            }
        }
    }

    public function toArray(\Traversable $iterator)
    {
        $iterator = $this->arrayCompatibleIterator($iterator);
        $array = [];
        
        foreach($iterator as $key => $value) {
            $array[$key] = $value;
        }
        
        return $array;
    }
    
    public function arrayCompatibleIterator(\Traversable $iterator)
    {
        return new ArrayCompatibleIterator($iterator);
    }
    
    protected function adapterIterator(\Traversable $iterator)
    {
        //No adapter needed, PHP 5.5 supports foreach of non scalar keys
        return $iterator;
    }
    
    public function arrayIterator(array $array)
    {
        return new ArrayIterator($array);
    }

    public function emptyIterator()
    {
        return new \EmptyIterator();
    }

    public function filterIterator(\Traversable $iterator, callable $predicate)
    {
        return new FilterIterator($iterator, $predicate);
    }

    public function projectionIterator(
            \Traversable $iterator, 
            callable $keyProjectionFunction = null, 
            callable $valueProjectionFunction = null)
    {
        return new ProjectionIterator(
                $iterator, 
                $keyProjectionFunction, 
                $valueProjectionFunction);
    }
    
    protected function joinIterator(
            Common\Joins\IInnerValuesJoiner $innerValuesJoiner, 
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $joiningFunction)
    {
        return new JoinIterator(
                $innerValuesJoiner, 
                $outerIterator, 
                $innerIterator, 
                $joiningFunction);
    }

    public function rangeIterator(\Traversable $iterator, $start, $amount)
    {
        return new RangeIterator($iterator, $start, $amount);
    }
    
    public function groupedIterator(
            \Traversable $iterator, 
            callable $groupKeyFunction,
            callable $traversableFactory)
    {
        return new GroupedIterator(
                $iterator, 
                $groupKeyFunction, 
                $traversableFactory);
    }

    public function orderedIterator(\Traversable $iterator, callable $function, $isAscending)
    {
        return new OrderedIterator($iterator, $function, $isAscending);
    }
    
    protected function setOperationIterator(\Traversable $iterator, Common\SetOperations\ISetFilter $setFilter)
    {
        return new SetOperationIterator($iterator, $setFilter);
    }
    
    protected function flattenedIteratorsIterator(\Traversable $iteratorsIterator)
    {
        return new FlatteningIterator($iteratorsIterator);
    }
}
