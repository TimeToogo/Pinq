<?php

namespace Pinq;

/**
 * An in-memory implementation for the traversable query api.
 * Making use of iterators to acheive lazy query execution.
 */
class Traversable implements \Pinq\ITraversable
{
    /**
     * @var Iterator 
     */
    protected $ValuesIterator;
    
    public function __construct($Values = [])
    {
        $this->ValuesIterator = is_array($Values) ? new \ArrayIterator($Values) : $Values;
    }
    
    final public function getIterator()
    {
        return $this->ValuesIterator;
    }
    
    public function AsArray() 
    {
        if($this->ValuesIterator instanceof \ArrayIterator) {
            return $this->ValuesIterator->getArrayCopy();
        }
        $Array = Utilities::ToArray($this->ValuesIterator);
        $this->ValuesIterator = new \ArrayIterator($Array);
        
        return $Array;
    }
    
    public function AsTraversable()
    {
        return $this;
    }
    
    public function AsCollection()
    {
        return new Collection($this->ValuesIterator);
    }
    
    public function AsQueryable()
    {
        return (new Providers\Traversable\Provider($this))->CreateQueryable();
    }
    
    public function AsRepository()
    {
        return (new Collection($this->ValuesIterator))->AsRepository();
    }
    
    // <editor-fold defaultstate="collapsed" desc="Querying">
    
    public function First() {
        foreach ($this->ValuesIterator as $Value) {
            return $Value;
        }
        
        return null;
    }
    
    public function Last() {
        $Array = $this->AsArray();
        return end($Array) ?: null;
    }
    public function Where(callable $Predicate) 
    {
        return new self(new Iterators\FilterIterator($this->ValuesIterator, $Predicate));
    }
    
    public function OrderByAscending(callable $Function) 
    {
        return new OrderedTraversable(new Iterators\OrderedIterator($this->ValuesIterator, $Function, true));
    }
    
    public function OrderByDescending(callable $Function) 
    {
        return new OrderedTraversable(new Iterators\OrderedIterator($this->ValuesIterator, $Function, false));
    }
    
    public function OrderBy(callable $Function, $Direction)
    {
        return $Direction === Direction::Descending ? $this->OrderByDescending($Function) : $this->OrderByAscending($Function);
    }
    
    public function Skip($Amount)
    {
        return new self(new Iterators\RangeIterator($this->ValuesIterator, $Amount, null));
    }
    
    public function Take($Amount) 
    {
        return new self(new Iterators\RangeIterator($this->ValuesIterator, 0, $Amount));
    }
    
    public function Slice($Start, $Amount) 
    {
        return new self(new Iterators\RangeIterator($this->ValuesIterator, $Start, $Amount));
    }
    
    public function IndexBy(callable $Function) 
    {
        return new self(new Iterators\ProjectionIterator($this->ValuesIterator, $Function, null));
    }
    
    public function GroupBy(callable $Function) 
    {
        return new GroupedTraversable(new Iterators\GroupedIterator($this->ValuesIterator, $Function));
    }
    
    public function Unique() 
    {
        return new self(new Iterators\UniqueIterator($this->ValuesIterator));
    }
    
    public function Select(callable $Function) 
    {
        return new self(new Iterators\ProjectionIterator($this->ValuesIterator, null, $Function));
    }
    
    public function SelectMany(callable $Function) 
    {
        $ProjectionIterator = new Iterators\ProjectionIterator($this->ValuesIterator, null, $Function);
        
        return new self(new Iterators\FlatteningIterator($ProjectionIterator));
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Operations">
    
    public function Union(ITraversable $Values)
    {
        return new self(new Iterators\UnionIterator($this->ValuesIterator, $Values->getIterator()));
    }
    
    public function Append(ITraversable $Values)
    {        
        return new self(new Iterators\FlatteningIterator(new \ArrayIterator([$this->ValuesIterator, $Values->getIterator()])));
    }
    
    public function Intersect(ITraversable $Values)
    {
        return new self(new Iterators\IntersectionIterator($this->ValuesIterator, $Values->getIterator()));
    }
    
    public function Except(ITraversable $Values)
    {
        return new self(new Iterators\ExceptIterator($this->ValuesIterator, $Values->getIterator()));
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Array Access">
    
    public function offsetExists($Index)
    {
        return $this->ValuesIterator instanceof \ArrayAccess ?
                $this->ValuesIterator->offsetExists($Index) : isset($this->AsArray()[$Index]);
    }

    public function offsetGet($Index)
    {
        return $this->ValuesIterator instanceof \ArrayAccess ?
                $this->ValuesIterator->offsetGet($Index) : $this->AsArray()[$Index];
    }

    public function offsetSet($Index, $Value)
    {
        throw PinqException::NotSupported(__METHOD__);
    }

    public function offsetUnset($Index)
    {
        throw PinqException::NotSupported(__METHOD__);
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Aggregates">
    
    public function Count() 
    {
        return $this->ValuesIterator instanceof \Countable ? 
                $this->ValuesIterator->count() : count($this->AsArray());
    }
    
    public function Exists() 
    {
        foreach ($this->ValuesIterator as $Value) {
            return true;
        }
        
        return false;
    }
    
    public function Contains($Value) 
    {
        foreach ($this->ValuesIterator as $ContainedValue) {
            if($ContainedValue === $Value) {
                return true;
            }
        }
        
        return false;
    }
    
    public function Aggregate(callable $Function) 
    {
        $HasValue = false;
        $AggregateValue = null;
        foreach ($this->AsArray() as $Value) {
            if(!$HasValue) {
                $AggregateValue = $Value;
                $HasValue = true;
                continue;
            }
            
            $AggregateValue = $Function($AggregateValue, $Value);
        }
        return $AggregateValue;
    }
    
    private function MapArray(callable $Function = null) 
    {
        if($Function === null) {
            return $this->AsArray();
        }
        else {
            return array_map($Function, $this->AsArray());
        }
    }
    
    public function Maximum(callable $Function = null) 
    {
        $Array = $this->MapArray($Function);
        return empty($Array) ? null : max($Array);
    }
    
    public function Minimum(callable $Function = null)
    {
        $Array = $this->MapArray($Function);
        return empty($Array) ? null : min($Array);
    }
    
    public function Sum(callable $Function = null) 
    {
        $Array = $this->MapArray($Function);
        return empty($Array) ? null : array_sum($Array);
    }
    
    public function Average(callable $Function = null)
    {
        $Array = $this->MapArray($Function);
        return empty($Array) ? null : array_sum($Array) / count($Array);
    }
    
    public function All(callable $Function = null) 
    {
        foreach ($this->MapArray($Function) as $Value) {
            if(!$Value) {
                return false;
            }
        }
        
        return true;
    }
    
    public function Any(callable $Function = null) 
    {
        foreach ($this->MapArray($Function) as $Value) {
            if($Value) {
                return true;
            }
        }
        
        return false;
    }
    
    public function Implode($Delimiter, callable $Function = null)
    {
        return implode($Delimiter, $this->MapArray($Function));
    }
    
    // </editor-fold>
}
