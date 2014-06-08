<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Iterator scheme using the extended iterator API. 
 * Compatible with PHP 5.4.0.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IteratorScheme extends Common\IteratorScheme
{
    public static function compatiableWith($phpVersion)
    {
        return version_compare($phpVersion, '5.4.0', '>=');
    }
    
    public function createOrderedMap(\Traversable $iterator = null)
    {
        return new OrderedMap($iterator == null ? null : $this->toIterator($iterator));
    }
    
    public function createOrderedMapFrom(array $keys, array $values)
    {
        return OrderedMap::from($keys, $values);
    }
    
    public function createSet(\Traversable $iterator = null)
    {
        return new Set($iterator == null ? null : $this->toIterator($iterator));
    }
    
    public function walk(\Traversable $iterator, callable $function)
    {
        $iterator = $this->adapter($iterator);
        $iterator->rewind();
        
        while($iterator->fetch($key, $value)
                && $function($value, $key) !== false) {
            
        }
    }

    public function toArray(\Traversable $iterator)
    {
        $iterator = $this->arrayCompatibleIterator($this->adapter($iterator));
        $array = [];
        
        $iterator->rewind();
        while($iterator->fetch($key, $value)) {
            $array[$key] = $value;
        }
        
        return $array;
    }
    
    /**
     * @param \Traversable $iterator
     * @return IIterator
     */
    private function adapter(\Traversable $iterator)
    {
        return $this->adapterIterator($iterator);
    }
    
    public function arrayCompatibleIterator(\Traversable $iterator)
    {
        return new ArrayCompatibleIterator($this->adapter($iterator));
    }
    
    public function adapterIterator(\Traversable $iterator)
    {
        if($iterator instanceof IIterator) {
            return $iterator;
        } elseif($iterator instanceof \Pinq\Iterators\Generators\IGenerator) {
            return new GeneratorAdapter($iterator); 
        } elseif($iterator instanceof \IteratorAggregate) {
            return $this->adapterIterator($iterator->getIterator());
        } else {
            return  new IteratorAdapter($iterator);
        }
    }

    public function arrayIterator(array $array)
    {
        return new ArrayIterator($array);
    }

    public function emptyIterator()
    {
        return new EmptyIterator();
    }

    public function filterIterator(\Traversable $iterator, callable $predicate)
    {
        return new FilterIterator($this->adapter($iterator), $predicate);
    }

    public function projectionIterator(
            \Traversable $iterator, 
            callable $keyProjectionFunction = null, 
            callable $valueProjectionFunction = null)
    {
        return new ProjectionIterator(
                $this->adapter($iterator), 
                $keyProjectionFunction, 
                $valueProjectionFunction);
    }
    
    public function reindexerIterator(\Traversable $iterator)
    {
        return new ReindexedIterator($this->adapter($iterator));
    }

    public function rangeIterator(\Traversable $iterator, $start, $amount)
    {
        return new RangeIterator($this->adapter($iterator), $start, $amount);
    }
    
    public function groupedIterator(
            \Traversable $iterator, 
            callable $groupKeyFunction, 
            callable $traversableFactory)
    {
        return new GroupedIterator(
                $this->adapter($iterator), 
                $groupKeyFunction, 
                $traversableFactory);
    }

    public function orderedIterator(\Traversable $iterator, callable $function, $isAscending)
    {
        return new OrderedIterator($this->adapter($iterator), $function, $isAscending);
    }
    
    protected function joinIterator(
            Common\Joins\IInnerValuesJoiner $innerValuesJoiner, 
            \Traversable $outerIterator, 
            \Traversable $innerIterator, 
            callable $joiningFunction)
    {
        return new JoinIterator(
                $innerValuesJoiner, 
                $this->adapter($outerIterator), 
                $this->adapter($innerIterator), 
                $joiningFunction);
    }
    
    protected function setOperationIterator(\Traversable $iterator, Common\SetOperations\ISetFilter $setFilter)
    {
        return new SetOperationIterator($this->adapter($iterator), $setFilter);
    }
    
    protected function flattenedIteratorsIterator(\Traversable $iteratorsIterator)
    {
        return new FlatteningIterator($this->adapter($iteratorsIterator));
    }
}
